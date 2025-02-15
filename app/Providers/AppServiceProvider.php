<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Create private directory if it doesn't exist
        $privateDir = storage_path('app/private');
        if (!file_exists($privateDir)) {
            mkdir($privateDir, 0755, true);
        }

        // Create database.json with proper permissions
        $jsonPath = $privateDir . '/database.json';
        if (!file_exists($jsonPath)) {
            $defaultData = ['api_calls' => []];
            file_put_contents($jsonPath, json_encode($defaultData, JSON_PRETTY_PRINT));
            chmod($jsonPath, 0644); // Make file writable
        }

        // Ensure the file is writable
        if (!is_writable($jsonPath)) {
            chmod($jsonPath, 0644);
        }
    }
}
