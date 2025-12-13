<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EnhancedLearningSeeder extends Seeder
{
    public function run(): void
    {
        // Create Gamification Badges
        $badges = [
            [
                'name' => 'First Steps',
                'slug' => 'first-steps',
                'description' => 'Complete your first lesson',
                'icon' => '🎯',
                'color' => '#10B981',
                'category' => 'milestone',
                'criteria_type' => 'lesson_completion',
                'criteria_data' => json_encode(['lessons_required' => 1]),
                'points_reward' => 10,
                'is_active' => true,
            ],
            [
                'name' => 'Course Completer',
                'slug' => 'course-completer',
                'description' => 'Complete your first course',
                'icon' => '🏆',
                'color' => '#F59E0B',
                'category' => 'achievement',
                'criteria_type' => 'course_completion',
                'criteria_data' => json_encode(['courses_required' => 1]),
                'points_reward' => 100,
                'is_active' => true,
            ],
            [
                'name' => 'Quiz Master',
                'slug' => 'quiz-master',
                'description' => 'Score 90% or higher on 5 quizzes',
                'icon' => '📝',
                'color' => '#3B82F6',
                'category' => 'achievement',
                'criteria_type' => 'assessment_score',
                'criteria_data' => json_encode(['min_score' => 90, 'count' => 5]),
                'points_reward' => 150,
                'is_active' => true,
            ],
            [
                'name' => 'Learning Streak',
                'slug' => 'learning-streak',
                'description' => 'Login and learn for 7 consecutive days',
                'icon' => '🔥',
                'color' => '#EF4444',
                'category' => 'special',
                'criteria_type' => 'streak',
                'criteria_data' => json_encode(['days_required' => 7]),
                'points_reward' => 75,
                'is_active' => true,
            ],
            [
                'name' => 'Knowledge Seeker',
                'slug' => 'knowledge-seeker',
                'description' => 'Earn 500 points',
                'icon' => '📚',
                'color' => '#8B5CF6',
                'category' => 'milestone',
                'criteria_type' => 'points_earned',
                'criteria_data' => json_encode(['points_required' => 500]),
                'points_reward' => 50,
                'is_active' => true,
            ],
        ];

        foreach ($badges as $badge) {
            DB::table('gamification_badges')->updateOrInsert(
                ['slug' => $badge['slug']],
                array_merge($badge, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        // Get some students and courses
        $students = DB::table('users')->where('role', 'student')->limit(5)->get();
        $courses = DB::table('courses')->limit(3)->get();

        if ($students->isEmpty() || $courses->isEmpty()) {
            $this->command->warn('No students or courses found. Skipping sample data.');
            return;
        }

        foreach ($students as $student) {
            foreach ($courses->take(2) as $course) {
                // Create Learner Progress
                DB::table('learner_progress')->updateOrInsert(
                    ['user_id' => $student->id, 'course_id' => $course->id],
                    [
                        'completion_percentage' => rand(10, 85),
                        'lessons_completed' => rand(1, 10),
                        'total_lessons' => rand(10, 15),
                        'time_spent_minutes' => rand(60, 300),
                        'last_accessed_at' => now()->subDays(rand(0, 7)),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );

                // Create Assessment Attempts
                for ($i = 1; $i <= rand(1, 3); $i++) {
                    $score = rand(60, 100);
                    DB::table('assessment_attempts')->insert([
                        'user_id' => $student->id,
                        'course_id' => $course->id,
                        'assessment_type' => 'quiz',
                        'attempt_number' => $i,
                        'score' => $score,
                        'max_score' => 100,
                        'percentage' => $score,
                        'time_taken_minutes' => rand(15, 45),
                        'status' => 'completed',
                        'started_at' => now()->subDays(rand(1, 10)),
                        'completed_at' => now()->subDays(rand(0, 9)),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                // Award some points
                $pointsSources = ['course_completion', 'quiz_passed', 'daily_login'];
                foreach ($pointsSources as $source) {
                    DB::table('learner_points')->insert([
                        'user_id' => $student->id,
                        'points' => rand(10, 50),
                        'source' => $source,
                        'source_id' => $course->id,
                        'source_type' => 'course',
                        'description' => ucfirst(str_replace('_', ' ', $source)),
                        'created_at' => now()->subDays(rand(0, 30)),
                        'updated_at' => now(),
                    ]);
                }
            }

            // Award some badges
            $badgeIds = DB::table('gamification_badges')->pluck('id')->take(2);
            foreach ($badgeIds as $badgeId) {
                DB::table('learner_badges')->updateOrInsert(
                    ['user_id' => $student->id, 'badge_id' => $badgeId],
                    [
                        'earned_at' => now()->subDays(rand(1, 30)),
                        'metadata' => json_encode(['auto_awarded' => true]),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }

        // Create AI Lecturers
        $aiLecturers = [
            [
                'name' => 'Professor Alex',
                'avatar' => '/avatars/professor-alex.png',
                'voice_id' => 'en-US-Neural2-A',
                'personality_traits' => 'Friendly, encouraging, patient, uses real-world examples',
                'teaching_style' => 'Socratic method, asks questions to guide learning',
                'expertise_area' => 'Technology & Programming',
                'prompt_template' => json_encode([
                    'system' => 'You are Professor Alex, a friendly and encouraging technology instructor.',
                    'style' => 'Use simple language, provide examples, ask guiding questions',
                ]),
                'is_active' => true,
            ],
            [
                'name' => 'Dr. Sarah Chen',
                'avatar' => '/avatars/dr-sarah.png',
                'voice_id' => 'en-US-Neural2-F',
                'personality_traits' => 'Analytical, detail-oriented, clear communicator',
                'teaching_style' => 'Step-by-step explanations with visual aids',
                'expertise_area' => 'Data Science & Analytics',
                'prompt_template' => json_encode([
                    'system' => 'You are Dr. Sarah Chen, an analytical data science expert.',
                    'style' => 'Provide structured explanations, use data examples, break down complex concepts',
                ]),
                'is_active' => true,
            ],
        ];

        foreach ($aiLecturers as $lecturer) {
            DB::table('ai_lecturers')->insert(array_merge($lecturer, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Create some WhatsApp reminders
        foreach ($students->take(2) as $student) {
            $course = $courses->first();
            DB::table('learner_course_reminders')->insert([
                'user_id' => $student->id,
                'course_id' => $course->id,
                'phone_number' => '+1234567890',
                'reminder_type' => 'completion',
                'message' => "Hi! You're 75% through '{$course->title}'. Keep going!",
                'scheduled_at' => now()->addDays(2),
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Enhanced learning features seeded successfully!');
        $this->command->info('Created: ' . count($badges) . ' badges');
        $this->command->info('Created: ' . count($aiLecturers) . ' AI lecturers');
    }
}
