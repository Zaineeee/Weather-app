<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

// API routes
Route::post('/api/register', [AuthController::class, 'register']);
Route::post('/api/login', [AuthController::class, 'login']);

// Catch-all route for SPA
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
 