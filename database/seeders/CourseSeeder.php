<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CourseSeeder extends Seeder
{
    /**
     * Seeds 6 courses, each with 3 modules of 3 lessons (54 lessons in total).
     * Safe to run more than once: rows are matched on slug / position.
     */
    public function run(): void
    {
        foreach ($this->catalogue() as $data) {
            $slug = Str::slug($data['title']);

            $course = Course::updateOrCreate(
                ['slug' => $slug],
                [
                    'title' => $data['title'],
                    'description' => $data['description'],
                    'level' => $data['level'],
                    'thumbnail_url' => "https://picsum.photos/seed/{$slug}/640/360",
                    'is_published' => $data['published'] ?? true,
                ]
            );

            foreach ($data['modules'] as $moduleIndex => [$moduleTitle, $lessons]) {
                $module = $course->modules()->updateOrCreate(
                    ['position' => $moduleIndex + 1],
                    ['title' => $moduleTitle]
                );

                foreach ($lessons as $lessonIndex => [$title, $type, $minutes]) {
                    $module->lessons()->updateOrCreate(
                        ['position' => $lessonIndex + 1],
                        [
                            'title' => $title,
                            'type' => $type,
                            'duration_minutes' => $minutes,
                            'content_url' => $type === 'quiz'
                                ? null
                                : 'https://example.com/content/'.Str::slug($title),
                        ]
                    );
                }
            }
        }
    }

    /** Each module is [title, [[lesson title, type, minutes], ...]]. */
    private function catalogue(): array
    {
        return [
            [
                'title' => 'Introduction to SQL',
                'level' => 'beginner',
                'description' => 'Learn to query relational data with SELECT, JOIN and GROUP BY using realistic school and course data.',
                'modules' => [
                    ['Getting started with databases', [
                        ['What is a relational database?', 'video', 8],
                        ['Tables, rows and keys', 'reading', 10],
                        ['Check your understanding', 'quiz', 5],
                    ]],
                    ['Querying data', [
                        ['SELECT, WHERE and ORDER BY', 'video', 12],
                        ['Filtering with AND, OR and IN', 'reading', 9],
                        ['Practice: write your first queries', 'quiz', 10],
                    ]],
                    ['Combining tables', [
                        ['INNER and LEFT JOIN', 'video', 14],
                        ['Grouping and aggregates', 'video', 13],
                        ['Final quiz: joins and groups', 'quiz', 10],
                    ]],
                ],
            ],
            [
                'title' => 'Web Development Fundamentals',
                'level' => 'beginner',
                'description' => 'Build a responsive web page from scratch with HTML, CSS and a little JavaScript.',
                'modules' => [
                    ['HTML essentials', [
                        ['How the web works', 'video', 9],
                        ['Structure of an HTML page', 'reading', 8],
                        ['HTML quiz', 'quiz', 5],
                    ]],
                    ['Styling with CSS', [
                        ['Selectors and the box model', 'video', 15],
                        ['Flexbox layouts', 'video', 12],
                        ['Responsive design basics', 'reading', 10],
                    ]],
                    ['Adding interaction', [
                        ['JavaScript variables and functions', 'video', 14],
                        ['Responding to clicks', 'video', 11],
                        ['Build a small page', 'quiz', 15],
                    ]],
                ],
            ],
            [
                'title' => 'Project Management Basics',
                'level' => 'beginner',
                'description' => 'Plan, track and deliver small projects using agile practices and simple tools.',
                'modules' => [
                    ['Project foundations', [
                        ['What makes a project?', 'video', 7],
                        ['Roles in a team', 'reading', 8],
                        ['Foundations quiz', 'quiz', 5],
                    ]],
                    ['Agile in practice', [
                        ['Scrum and Kanban compared', 'video', 13],
                        ['Writing good user stories', 'reading', 10],
                        ['Estimating work', 'video', 11],
                    ]],
                    ['Delivering and reviewing', [
                        ['Tracking progress', 'video', 9],
                        ['Running a retrospective', 'reading', 8],
                        ['Case study quiz', 'quiz', 12],
                    ]],
                ],
            ],
            [
                'title' => 'Cloud Computing with AWS',
                'level' => 'intermediate',
                'description' => 'Understand core AWS services and deploy a simple web application securely.',
                'modules' => [
                    ['Cloud concepts', [
                        ['Regions and availability zones', 'video', 10],
                        ['The shared responsibility model', 'reading', 8],
                        ['Concepts quiz', 'quiz', 5],
                    ]],
                    ['Compute and storage', [
                        ['Launching an EC2 instance', 'video', 16],
                        ['Storing files in S3', 'video', 12],
                        ['Managed databases with RDS', 'reading', 10],
                    ]],
                    ['Security and operations', [
                        ['IAM roles and least privilege', 'video', 14],
                        ['Monitoring with CloudWatch', 'video', 11],
                        ['Deployment checklist quiz', 'quiz', 10],
                    ]],
                ],
            ],
            [
                'title' => 'Python for Data Analysis',
                'level' => 'intermediate',
                'description' => 'Clean, explore and chart data with Python using pandas and simple visualisations.',
                'modules' => [
                    ['Python refresher', [
                        ['Lists, dictionaries and loops', 'video', 13],
                        ['Writing reusable functions', 'reading', 9],
                        ['Refresher quiz', 'quiz', 6],
                    ]],
                    ['Working with pandas', [
                        ['DataFrames and series', 'video', 15],
                        ['Cleaning missing data', 'video', 12],
                        ['Grouping and summarising', 'reading', 10],
                    ]],
                    ['Telling the story', [
                        ['Choosing the right chart', 'video', 9],
                        ['Plotting with matplotlib', 'video', 13],
                        ['Capstone mini-analysis', 'quiz', 20],
                    ]],
                ],
            ],
            [
                'title' => 'Advanced Laravel APIs',
                'level' => 'advanced',
                'description' => 'Design secure, well-tested REST APIs with queues, policies and API resources.',
                'published' => false, // draft course: should NOT appear in the learner portal
                'modules' => [
                    ['API design', [
                        ['Resource-oriented design', 'video', 12],
                        ['Versioning and pagination', 'reading', 9],
                        ['Design quiz', 'quiz', 5],
                    ]],
                    ['Security', [
                        ['Authentication with Sanctum', 'video', 15],
                        ['Policies and gates', 'video', 12],
                        ['Security quiz', 'quiz', 8],
                    ]],
                    ['Quality at scale', [
                        ['Background jobs and queues', 'video', 14],
                        ['Feature testing', 'video', 13],
                        ['Release checklist', 'reading', 7],
                    ]],
                ],
            ],
        ];
    }
}
