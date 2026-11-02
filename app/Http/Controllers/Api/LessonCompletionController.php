<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Enrolment;
use App\Models\Lesson;
use App\Models\LessonCompletion;
use Illuminate\Http\Request;

class LessonCompletionController extends Controller
{
    
    public function complete(Request $request, Lesson $lesson)
    {
        $user = $request->user();

        //load module and course
        $lesson->load('module.course');
        $course = $lesson->module->course;

        //check course is exist and published
        if (!$course || !$course->is_published) {
            return response()->json([
                'status' => 'error',
                'message' => 'Course not found'
            ], 404);
        }

        //check user is enrolled to the course
        $isEnrolled = Enrolment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->exists();

        if (!$isEnrolled) {
            return response()->json([
                'status' => 'error',
                'message' => 'You must be enrolled in this course to complete its lessons'
            ], 403);
        }

        //safe to call multiple times without error
        LessonCompletion::firstOrCreate([
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
        ]);

        //calculate updated course progress
        $totalLessons = $course->lessons()->count();
        $completedLessons = LessonCompletion::where('user_id', $user->id)
            ->whereIn('lesson_id', $course->lessons()->pluck('lessons.id'))
            ->count();

        $percentage = $totalLessons > 0 ? (int) round(($completedLessons / $totalLessons) * 100) : 0;

        return response()->json([
            'status' => 'success',
            'message' => 'Lesson marked as completed',
            'data' => [
                'lesson_id' => $lesson->id,
                'course_id' => $course->id,
                'progress' => [
                    'completed_lessons' => $completedLessons,
                    'total_lessons' => $totalLessons,
                    'percentage' => $percentage,
                ]
            ]
        ], 200);
    }
}
