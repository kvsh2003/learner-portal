<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
   //get all published courses
    public function index(Request $request)
    {
        $courses = Course::where('is_published', true)
        //search courses by title
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->query('search') . '%');
            })
            ->withCount(['modules', 'lessons'])
            ->orderBy('id', 'asc')
            ->paginate(6);

        return CourseResource::collection($courses);
    }

   
    public function show(string $slug)
    {
        $course = Course::where('slug', $slug)
            ->where('is_published', true)
            ->with([
                'modules' => function ($query) {
                    $query->orderBy('position')->with([
                        'lessons' => function ($lessonQuery) {
                            $lessonQuery->orderBy('position');
                        }
                    ]);
                }
            ])
            ->first();

        if (!$course) {
            return response()->json([
                'status' => 'error',
                'message' => 'Course not found'
            ], 404);
        }

        return new CourseResource($course);
    }
}
