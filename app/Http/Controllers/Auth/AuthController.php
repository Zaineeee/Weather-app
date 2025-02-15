<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\LoginHistory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);

            // Admin login check
            if ($credentials['email'] === 'admin@weather.com' && $credentials['password'] === 'admin123') {
                return response()->json([
                    'userId' => 'admin',
                    'name' => 'Admin',
                    'email' => $credentials['email'],
                    'user' => [
                        'userId' => 'admin',
                        'name' => 'Admin',
                        'email' => $credentials['email']
                    ]
                ]);
            }

            $user = User::where('email', $credentials['email'])->first();

            if (!$user || !Hash::check($credentials['password'], $user->password)) {
                return response()->json([
                    'error' => 'Invalid credentials'
                ], 401);
            }

            // First, update any existing active sessions to logged out
            LoginHistory::where('user_id', $user->id)
                ->whereNull('logout_time')
                ->update([
                    'logout_time' => now()
                ]);

            // Then create new login record
            try {
                LoginHistory::create([
                    'user_id' => $user->id,
                    'ip_address' => $request->ip(),
                    'created_at' => now(),
                    'logout_time' => null // Explicitly set to null for new session
                ]);
                Log::info('Login history recorded for user: ' . $user->id);
            } catch (\Exception $e) {
                Log::error('Failed to record login history: ' . $e->getMessage());
            }

            return response()->json([
                'userId' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'user' => [
                    'userId' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Login failed'
            ], 500);
        }
    }

    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
                'phone' => 'required|string',
                'address' => 'required|string',
                'userId' => 'required|string|unique:users,unique_id'
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'address' => $request->address,
                'unique_id' => $request->userId
            ]);

            // Record first login
            LoginHistory::create([
                'user_id' => $user->id,
                'ip_address' => $request->ip()
            ]);

            return response()->json([
                'message' => 'Registration successful',
                'user' => $user
            ], 201);
        } catch (\Exception $e) {
            Log::error('Registration error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 