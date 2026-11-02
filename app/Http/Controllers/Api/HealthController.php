<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HealthController extends Controller
{
    //check health in database
     public function check()
    {
        try {
            DB::connection()->getPdo();

            return response()->json([
                'status' => 'healthy',
                'api' => 'running',
                'database' => 'connected',
            ], 200);

        } catch (Exception $e) {

            Log::error('Health check failed', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'unhealthy',
                'database' => 'disconnected',
            ], 503);
        }
    }
}
