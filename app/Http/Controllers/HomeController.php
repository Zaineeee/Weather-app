<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        // Remove auth middleware from constructor since landing page is public
        $this->middleware('auth')->except('index');
    }

    /**
     * Show the application landing page.
     */
    public function index()
    {
        return view('home');
    }
}