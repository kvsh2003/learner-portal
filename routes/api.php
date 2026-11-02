<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\HealthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


// public APIs

//login
Route::post('login',[AuthController::class,'login']);
//health
Route::get('/health', [HealthController::class, 'check']);

//course
Route::get('/courses', [CourseController::class, 'index']);

//course/{slug}
Route::get('/courses/{slug}', [CourseController::class, 'show']);


// Protected APIs
Route::middleware('auth:sanctum')->group(function () {
    // Authentication
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class,'me']);
    // Enrollments
    //lesson completion
});
