<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\CourseCategory;
use App\Models\Instructor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user if doesn't exist
        $admin = User::firstOrCreate(
            ['email' => 'admin@carterlms.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Create instructors
        $instructors = [];
        $instructorData = [
            ['name' => 'John Smith', 'email' => 'john@carterlms.com', 'bio' => 'Expert in Web Development with 10+ years experience'],
            ['name' => 'Sarah Johnson', 'email' => 'sarah@carterlms.com', 'bio' => 'Data Science and Machine Learning Specialist'],
            ['name' => 'Michael Chen', 'email' => 'michael@carterlms.com', 'bio' => 'UI/UX Designer and Frontend Developer'],
            ['name' => 'Emily Davis', 'email' => 'emily@carterlms.com', 'bio' => 'Business and Marketing Strategist'],
        ];

        foreach ($instructorData as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'role' => 'instructor',
                    'email_verified_at' => now(),
                ]
            );

            $instructor = Instructor::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'biography' => $data['bio'],
                    'designation' => 'Senior Instructor',
                    'status' => 'approved',
                ]
            );

            $instructors[] = $instructor;
        }

        // Create students
        for ($i = 1; $i <= 20; $i++) {
            User::firstOrCreate(
                ['email' => "student$i@carterlms.com"],
                [
                    'name' => "Student $i",
                    'password' => Hash::make('password'),
                    'role' => 'student',
                    'email_verified_at' => now(),
                ]
            );
        }

        // Get categories
        $categories = CourseCategory::all();

        // Create sample courses
        $courses = [
            [
                'title' => 'Complete Web Development Bootcamp',
                'slug' => 'complete-web-development-bootcamp',
                'description' => 'Learn HTML, CSS, JavaScript, React, Node.js and more. Become a full-stack web developer.',
                'short_description' => 'Master web development from scratch',
                'level' => 'beginner',
                'language' => 'English',
                'price' => 99.99,
                'category_id' => $categories->first()->id ?? 1,
                'instructor_id' => $instructors[0]->id ?? 1,
            ],
            [
                'title' => 'Python for Data Science and Machine Learning',
                'slug' => 'python-data-science-machine-learning',
                'description' => 'Learn Python, Pandas, NumPy, Matplotlib, Seaborn, Scikit-Learn, and Machine Learning.',
                'short_description' => 'Master Python for data analysis',
                'level' => 'intermediate',
                'language' => 'English',
                'price' => 129.99,
                'category_id' => $categories->skip(1)->first()->id ?? 1,
                'instructor_id' => $instructors[1]->id ?? 1,
            ],
            [
                'title' => 'Modern UI/UX Design with Figma',
                'slug' => 'modern-ui-ux-design-figma',
                'description' => 'Learn professional UI/UX design principles and master Figma from beginner to advanced.',
                'short_description' => 'Design beautiful user interfaces',
                'level' => 'beginner',
                'language' => 'English',
                'price' => 79.99,
                'category_id' => $categories->skip(2)->first()->id ?? 1,
                'instructor_id' => $instructors[2]->id ?? 1,
            ],
            [
                'title' => 'Digital Marketing Mastery',
                'slug' => 'digital-marketing-mastery',
                'description' => 'Complete guide to SEO, Social Media Marketing, Email Marketing, and Content Marketing.',
                'short_description' => 'Master digital marketing strategies',
                'level' => 'intermediate',
                'language' => 'English',
                'price' => 89.99,
                'category_id' => $categories->skip(3)->first()->id ?? 1,
                'instructor_id' => $instructors[3]->id ?? 1,
            ],
            [
                'title' => 'React - The Complete Guide',
                'slug' => 'react-complete-guide',
                'description' => 'Dive deep into React.js - Hooks, Redux, Next.js, TypeScript and modern web development.',
                'short_description' => 'Build modern React applications',
                'level' => 'intermediate',
                'language' => 'English',
                'price' => 109.99,
                'category_id' => $categories->first()->id ?? 1,
                'instructor_id' => $instructors[0]->id ?? 1,
            ],
            [
                'title' => 'Mobile App Development with Flutter',
                'slug' => 'flutter-mobile-app-development',
                'description' => 'Build beautiful native mobile apps for iOS and Android using Flutter and Dart.',
                'short_description' => 'Create cross-platform mobile apps',
                'level' => 'intermediate',
                'language' => 'English',
                'price' => 99.99,
                'category_id' => $categories->first()->id ?? 1,
                'instructor_id' => $instructors[0]->id ?? 1,
            ],
        ];

        foreach ($courses as $courseData) {
            DB::table('courses')->insert(array_merge($courseData, [
                'status' => 'published',
                'is_featured' => rand(0, 1),
                'duration' => rand(5, 40) . ' hours',
                'thumbnail' => '/assets/images/course-placeholder.jpg',
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        $this->command->info('Demo data created successfully!');
        $this->command->info('Admin: admin@carterlms.com / password');
        $this->command->info('Instructor: john@carterlms.com / password');
        $this->command->info('Student: student1@carterlms.com / password');
    }
}
