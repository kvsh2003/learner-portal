<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Enrolment;
use App\Models\Lesson;
use App\Models\LessonCompletion;
use App\Models\User;
use Database\Seeders\CourseSeeder;
use Database\Seeders\LearnerSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LearnerPortalApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Add sample learners and courses
        $this->seed(LearnerSeeder::class);
        $this->seed(CourseSeeder::class);
    }

    /**
     * Test 1: Check if the API and database are working.
     */
    public function test_health_check_returns_200_and_confirms_database(): void
    {
        $response = $this->getJson('/api/health');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'healthy',
                'api' => 'running',
                'database' => 'connected',
            ]);
    }

    /**
     * Test 2: Check learner login and profile access.
     */
    public function test_learner_can_login_and_access_profile(): void
    {
        // Login with correct details
        $loginResponse = $this->postJson('/api/login', [
            'email' => 'amara@example.com',
            'password' => 'password',
        ]);

        $loginResponse->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'token',
                'user' => ['id', 'name', 'email'],
            ]);

        $token = $loginResponse->json('token');

        // Get learner profile using the token
        $meResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/me');

        $meResponse->assertStatus(200)
            ->assertJsonPath('user.email', 'amara@example.com');

        // Login with wrong password
        $badLogin = $this->postJson('/api/login', [
            'email' => 'amara@example.com',
            'password' => 'wrong-password',
        ]);

        $badLogin->assertStatus(401);
    }

    /**
     * Test 3: Check if draft courses are hidden.
     */
    public function test_draft_courses_stay_hidden_from_catalogue_and_detail(): void
    {
        // Get all published courses
        $response = $this->getJson('/api/courses');

        $response->assertStatus(200);

        // Check that only published courses are shown
        $courses = $response->json('data');
        $this->assertNotEmpty($courses);

        foreach ($courses as $course) {
            $this->assertTrue($course['is_published']);
            $this->assertNotEquals('advanced-laravel-apis', $course['slug']);
        }

        // Check that a draft course cannot be viewed
        $draftResponse = $this->getJson('/api/courses/advanced-laravel-apis');
        $draftResponse->assertStatus(404);
    }

    /**
     * Test 4: Check course enrolment and duplicate enrolment.
     */
    public function test_learner_can_enrol_in_course_and_duplicate_enrolment_is_handled(): void
    {
        $learner = User::where('email', 'amara@example.com')->first();
        Sanctum::actingAs($learner);

        $course = Course::where('is_published', true)->first();

        // Enrol in the course
        $response = $this->postJson("/api/courses/{$course->id}/enrol");
        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'message' => 'Successfully enrolled in course',
            ]);

        $this->assertDatabaseHas('enrolments', [
            'user_id' => $learner->id,
            'course_id' => $course->id,
        ]);

        // Try to enrol in the same course again
        $duplicateResponse = $this->postJson("/api/courses/{$course->id}/enrol");
        $duplicateResponse->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Already enrolled in this course',
            ]);

        // Check that only one enrolment record exists
        $enrolmentCount = Enrolment::where('user_id', $learner->id)
            ->where('course_id', $course->id)
            ->count();
        $this->assertEquals(1, $enrolmentCount);
    }

    /**
     * Test 5: Check that learners cannot complete lessons without enrolling.
     */
    public function test_completing_lesson_without_being_enrolled_is_forbidden(): void
    {
        $learner = User::where('email', 'amara@example.com')->first();
        Sanctum::actingAs($learner);

        // Get a lesson from a course the learner has not joined
        $course = Course::where('is_published', true)->first();
        $lesson = $course->lessons()->first();

        $response = $this->postJson("/api/lessons/{$lesson->id}/complete");

        $response->assertStatus(403)
            ->assertJson([
                'status' => 'error',
                'message' => 'You must be enrolled in this course to complete its lessons',
            ]);

        // Check that the lesson was not marked as completed
        $this->assertDatabaseMissing('lesson_completions', [
            'user_id' => $learner->id,
            'lesson_id' => $lesson->id,
        ]);
    }

    /**
     * Test 6: Check lesson completion and course progress.
     */
    public function test_enrolled_learner_can_complete_lesson_and_progress_updates(): void
    {
        $learner = User::where('email', 'amara@example.com')->first();
        Sanctum::actingAs($learner);

        $course = Course::where('is_published', true)->first();

        // Enrol the learner in the course
        Enrolment::create([
            'user_id' => $learner->id,
            'course_id' => $course->id,
        ]);

        $lesson = $course->lessons()->first();

        // Mark the lesson as completed
        $response = $this->postJson("/api/lessons/{$lesson->id}/complete");
        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.progress.completed_lessons', 1);

        // Complete the same lesson again
        $repeatResponse = $this->postJson("/api/lessons/{$lesson->id}/complete");
        $repeatResponse->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.progress.completed_lessons', 1);

        // Check that only one completion record exists
        $completionCount = LessonCompletion::where('user_id', $learner->id)
            ->where('lesson_id', $lesson->id)
            ->count();
        $this->assertEquals(1, $completionCount);

        // Check course progress in the enrolment list
        $enrolmentsResponse = $this->getJson('/api/me/enrolments');
        $enrolmentsResponse->assertStatus(200)
            ->assertJsonPath('data.0.progress.completed_lessons', 1)
            ->assertJsonPath('data.0.progress.percentage', 11); // 1 out of 9 lessons = 11%
    }
}
