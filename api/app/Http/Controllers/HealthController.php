<?php

namespace App\Http\Controllers;

class HealthController extends Controller
{
    public function index()
    {
        return response()->json([
            'message' => 'Hello World from PHP (Nginx + Apache + Lumen + PostgreSQL)'
        ], 200);
    }
}
