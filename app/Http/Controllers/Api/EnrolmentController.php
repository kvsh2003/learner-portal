<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EnrolmentResource;
use App\Models\Course;
use App\Models\Enrolment;
use Illuminate\Http\Request;

class EnrolmentController extends Controller
{
    
    public function enrol(Request $request, Course $course)
    {
        // if course is not published return error
        if (!$course->is_published) {
            return response()->json([
                'status' => 'error',
                'message' => 'Course not found'
            ], 404);
        }

        $user = $request->user();

        // check if user is already enrolled in the course
        $existing = Enrolment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($existing) {
            return response()->json([
                'status' => 'success',
                'message' => 'Already enrolled in this course',
                'data' => $existing,
            ], 200);
        }

        $enrolment = Enrolment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully enrolled in course',
            'data' => $enrolment,
        ], 201);
    }

    // get user enrolments
    public function myEnrolments(Request $request)
    {
        $user = $request->user();

        
        $enrolments = $user->enrolments()
            ->with([
                'course' => function ($query) {
                    $query->where('is_published', true)->with('lessons:id,module_id');
                }
            ])
            ->latest()
            ->get()
            ->filter(fn ($enrolment) => $enrolment->course !== null);

        // get completed lesson ids
        $completedLessonIds = $user->lessonCompletions()
            ->pluck('lesson_id')
            ->flip();



            // calculate progress for each course
        $data = $enrolments->map(function ($enrolment) use ($completedLessonIds) {
            $course = $enrolment->course;
            $totalLessons = $course->lessons->count();

            $completedCount = $course->lessons->filter(function ($lesson) use ($completedLessonIds) {
                return isset($completedLessonIds[$lesson->id]);
            })->count();

            $percentage = $totalLessons > 0 ? (int) round(($completedCount / $totalLessons) * 100) : 0;

            $enrolment->progress = [
                'completed_lessons' => $completedCount,
                'total_lessons' => $totalLessons,
                'percentage' => $percentage,
            ];

            return new EnrolmentResource($enrolment);
        });

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ], 200);
    }
}
