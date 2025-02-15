<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    public function index()
    {
        return response()->json([
            'apiKey' => config('services.openweather.key')
        ]);
    }
} 