<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\LoginHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class UserController extends Controller
{
    public function index()
    {
        try {
            $users = User::select(
                'users.id',
                'users.unique_id as userId',
                'users.name',
                'users.email',
                'users.phone',
                'users.address',
                'users.created_at as createdDate'
            )
            ->leftJoin('login_history', function($join) {
                $join->on('users.id', '=', 'login_history.user_id')
                    ->whereRaw('login_history.created_at = (
                        SELECT MAX(created_at)
                        FROM login_history
                        WHERE user_id = users.id
                    )');
            })
            ->addSelect(DB::raw('COALESCE(login_history.created_at, NULL) as lastLogin'))
            ->get();

            return response()->json($users);
        } catch (\Exception $e) {
            Log::error('Failed to fetch users: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch users',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function loginHistory()
    {
        try {
            $loginHistory = DB::table('login_history')
                ->join('users', 'users.id', '=', 'login_history.user_id')
                ->select(
                    'login_history.id',
                    'users.email',
                    DB::raw('DATE_FORMAT(login_history.created_at, "%Y-%m-%d %H:%i:%s") as loginTime'),
                    DB::raw('CASE 
                        WHEN login_history.logout_time IS NULL AND login_history.created_at >= NOW() - INTERVAL 24 HOUR THEN "Active"
                        ELSE "Inactive"
                    END as sessionStatus'),
                    'login_history.logout_time as logoutTime'
                )
                ->whereIn('login_history.id', function($query) {
                    $query->select(DB::raw('MAX(id)'))
                        ->from('login_history')
                        ->groupBy('user_id');
                })
                ->orderBy('login_history.created_at', 'desc')
                ->get();

            Log::info('Login history fetched', [
                'count' => count($loginHistory),
                'sample' => $loginHistory->first()
            ]);
            
            return response()->json($loginHistory);
        } catch (\Exception $e) {
            Log::error('Failed to fetch login history: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch login history',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function createdDates()
    {
        try {
            $users = User::select(
                'name',
                'email',
                'created_at as createdDate'
            )
            ->orderBy('created_at', 'desc')
            ->get();

            return response()->json($users);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch user creation dates',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function recordLogout(Request $request)
    {
        try {
            $latestLogin = LoginHistory::where('user_id', auth()->id())
                ->whereNull('logout_time')
                ->latest('created_at')
                ->first();

            if ($latestLogin) {
                $latestLogin->update([
                    'logout_time' => now()
                ]);
                
                Log::info('Logout recorded', [
                    'user_id' => auth()->id(),
                    'login_id' => $latestLogin->id,
                    'logout_time' => $latestLogin->logout_time
                ]);

                // Force refresh login history after logout
                Cache::forget('login_history_' . auth()->id());
            }

            return response()->json(['message' => 'Logout recorded successfully']);
        } catch (\Exception $e) {
            Log::error('Failed to record logout', ['error' => $e->getMessage()]);
            return response()->json([
                'error' => 'Failed to record logout',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'address' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:20'
            ]);

            $user->update($validated);
            
            return response()->json(['message' => 'User updated successfully']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to update user',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Begin transaction
            DB::beginTransaction();
            
            try {
                // Delete related records first
                LoginHistory::where('user_id', $id)->delete();
                
                // Delete the user
                $user->delete();
                
                // Commit transaction
                DB::commit();
                
                return response()->json([
                    'message' => 'User deleted successfully'
                ]);
                
            } catch (\Exception $e) {
                // Rollback in case of error
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error('Failed to delete user: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to delete user: ' . $e->getMessage()
            ], 500);
        }
    }
} 