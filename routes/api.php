<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ApiTrackingController;

// Auth routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Dashboard data routes
Route::get('/users', [UserController::class, 'index']);
Route::get('/login-history', [UserController::class, 'loginHistory']);
Route::get('/api-tracking', [ApiTrackingController::class, 'index']);
Route::get('/api-statistics', [ApiTrackingController::class, 'statistics']);
Route::get('/created-dates', [UserController::class, 'createdDates']);
Route::post('/record-logout', [UserController::class, 'recordLogout'])->middleware('auth');
Route::put('/users/{id}', [UserController::class, 'update']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);

// Other API routes... 

// Add these routes
Route::post('/track-api-call', [ApiTrackingController::class, 'store']);
Route::get('/track-api-call', [ApiTrackingController::class, 'index']);