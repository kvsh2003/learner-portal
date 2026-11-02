<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\EnrolmentController;
use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\LessonCompletionController;
use Illuminate\Support\Facades\Route;

// Public APIs
Route::post('login', [AuthController::class, 'login']);
Route::get('health', [HealthController::class, 'check']);
Route::get('courses', [CourseController::class, 'index']);
Route::get('courses/{slug}', [CourseController::class, 'show']);

// Protected APIs (Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    // Authentication
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);

    // Enrolments & Progress
    Route::post('courses/{course}/enrol', [EnrolmentController::class, 'enrol']);
    Route::get('me/enrolments', [EnrolmentController::class, 'myEnrolments']);

    // Lesson Completion
    Route::post('lessons/{lesson}/complete', [LessonCompletionController::class, 'complete']);
});
