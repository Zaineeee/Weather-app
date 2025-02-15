<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class ApiTrackingController extends Controller
{
    private $jsonPath;

    public function __construct()
    {
        $this->jsonPath = storage_path('app/private/database.json');
    }

    public function store(Request $request)
    {
        try {
            // Ensure directory exists and is writable
            $directory = dirname($this->jsonPath);
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            // Ensure file exists and is writable
            if (!file_exists($this->jsonPath)) {
                file_put_contents($this->jsonPath, json_encode(['api_calls' => []], JSON_PRETTY_PRINT));
                chmod($this->jsonPath, 0644);
            }

            // Get user information with better fallbacks
            $userId = $request->input('userId', 'Unknown');
            $userName = $request->input('userName', 'Unknown User');
            $userEmail = 'N/A';

            // Try to fetch email from database if we have a valid user ID
            if ($userId !== 'Unknown' && $userId !== 'undefined') {
                $user = User::find($userId);
                if ($user) {
                    $userEmail = $user->email;
                }
            }

            $endpoint = $request->input('endpoint', 'weather-api');
            $status = $request->input('status', 'success');

            // Create or read the JSON file
            $jsonData = json_decode(file_get_contents($this->jsonPath), true) ?? ['api_calls' => []];

            // Create new API call entry
            $newCall = [
                'id' => count($jsonData['api_calls']) + 1,
                'user_id' => $userId,
                'user_name' => $userName,
                'email' => $userEmail,
                'endpoint' => $endpoint,
                'status' => $status,
                'timestamp' => Carbon::now()->format('Y-m-d H:i:s')
            ];

            // Add new call to array
            $jsonData['api_calls'][] = $newCall;

            // Write to file with proper permissions
            $success = file_put_contents($this->jsonPath, json_encode($jsonData, JSON_PRETTY_PRINT));

            if ($success === false) {
                throw new \Exception('Failed to write to database.json');
            }

            return response()->json([
                'message' => 'API call tracked successfully',
                'data' => $newCall
            ]);

        } catch (\Exception $e) {
            Log::error('API tracking error', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'error' => 'Failed to track API call',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function index()
    {
        try {
            if (!file_exists($this->jsonPath)) {
                return response()->json([
                    'userStats' => [],
                    'detailedCalls' => [],
                    'statistics' => [
                        'totalCalls' => 0,
                        'todayCalls' => 0,
                        'avgCalls' => 0
                    ]
                ]);
            }

            $jsonData = json_decode(file_get_contents($this->jsonPath), true);
            $apiCalls = $jsonData['api_calls'] ?? [];

            // Sort by timestamp descending
            usort($apiCalls, function($a, $b) {
                return strtotime($b['timestamp']) - strtotime($a['timestamp']);
            });

            // Calculate statistics
            $today = Carbon::today();
            $todayCalls = count(array_filter($apiCalls, function($call) {
                return Carbon::parse($call['timestamp'])->isToday();
            }));

            // Group by user
            $userStats = [];
            foreach ($apiCalls as $call) {
                $userId = $call['user_id'];
                if (!isset($userStats[$userId])) {
                    $userStats[$userId] = [
                        'userId' => $userId,
                        'userName' => $call['user_name'],
                        'email' => $call['email'],
                        'totalCalls' => 0,
                        'lastCall' => null
                    ];
                }
                $userStats[$userId]['totalCalls']++;
                $userStats[$userId]['lastCall'] = max($userStats[$userId]['lastCall'] ?? '', $call['timestamp']);
            }

            return response()->json([
                'userStats' => array_values($userStats),
                'detailedCalls' => $apiCalls,
                'statistics' => [
                    'totalCalls' => count($apiCalls),
                    'todayCalls' => $todayCalls,
                    'avgCalls' => $this->calculateAvgCalls($apiCalls)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to fetch API calls: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch API calls'], 500);
        }
    }

    private function calculateAvgCalls($calls)
    {
        if (empty($calls)) return 0;
        $firstCall = Carbon::parse(min(array_column($calls, 'timestamp')));
        $daysActive = max(1, $firstCall->diffInDays(Carbon::today()) + 1);
        return round(count($calls) / $daysActive, 2);
    }
} 