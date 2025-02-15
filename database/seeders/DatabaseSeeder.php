<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\LoginHistory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create test user
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'phone' => '1234567890',
            'address' => '123 Test St',
            'unique_id' => 'TEST001'
        ]);

        // Create some login history records
        LoginHistory::create([
            'user_id' => $user->id,
            'ip_address' => '127.0.0.1',
            'created_at' => now()->subHours(2),
            'logout_time' => now()->subHours(1)
        ]);

        LoginHistory::create([
            'user_id' => $user->id,
            'ip_address' => '127.0.0.1',
            'created_at' => now(),
            'logout_time' => null // Active session
        ]);

        // Create additional test users with login history
        for ($i = 1; $i <= 5; $i++) {
            $user = User::create([
                'name' => "Test User {$i}",
                'email' => "test{$i}@example.com",
                'password' => Hash::make('password'),
                'phone' => "123456789{$i}",
                'address' => "{$i}23 Test St",
                'unique_id' => "TEST00{$i}"
            ]);

            LoginHistory::create([
                'user_id' => $user->id,
                'ip_address' => '127.0.0.1',
                'created_at' => now()->subHours($i),
                'logout_time' => now()->subMinutes($i * 30)
            ]);
        }
    }
}
