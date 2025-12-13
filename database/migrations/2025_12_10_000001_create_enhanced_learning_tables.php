<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Learner Progress - Course completion tracking
        Schema::create('learner_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->integer('completion_percentage')->default(0);
            $table->integer('lessons_completed')->default(0);
            $table->integer('total_lessons')->default(0);
            $table->integer('time_spent_minutes')->default(0);
            $table->timestamp('last_accessed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'course_id']);
        });

        // Assessment Attempts - Quiz attempt history
        Schema::create('assessment_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('quiz_id')->nullable();
            $table->string('assessment_type')->default('quiz'); // quiz, exam, assignment
            $table->integer('attempt_number')->default(1);
            $table->integer('score')->default(0);
            $table->integer('max_score')->default(100);
            $table->decimal('percentage', 5, 2)->default(0);
            $table->integer('time_taken_minutes')->nullable();
            $table->string('status')->default('completed'); // completed, in_progress, abandoned
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        // Gamification Badges - Achievement badges
        Schema::create('gamification_badges', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('color')->default('#3B82F6');
            $table->string('category')->default('achievement'); // achievement, milestone, special
            $table->string('criteria_type'); // course_completion, points_earned, streak, assessment_score
            $table->json('criteria_data')->nullable(); // JSON with specific requirements
            $table->integer('points_reward')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Learner Badges - Badges earned by employees
        Schema::create('learner_badges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('badge_id')->references('id')->on('gamification_badges')->onDelete('cascade');
            $table->timestamp('earned_at');
            $table->json('metadata')->nullable(); // Additional context about how badge was earned
            $table->timestamps();
            
            $table->unique(['user_id', 'badge_id']);
        });

        // Learner Points - Points/XP earned
        Schema::create('learner_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('points')->default(0);
            $table->string('source'); // course_completion, quiz_passed, badge_earned, daily_login, streak
            $table->unsignedBigInteger('source_id')->nullable(); // ID of course, quiz, badge, etc.
            $table->string('source_type')->nullable(); // course, quiz, badge, etc.
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // AI Lecturers - Virtual instructor personas
        Schema::create('ai_lecturers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('avatar')->nullable();
            $table->string('voice_id')->nullable(); // For text-to-speech
            $table->text('personality_traits')->nullable();
            $table->text('teaching_style')->nullable();
            $table->string('expertise_area')->nullable();
            $table->json('prompt_template')->nullable(); // AI persona instructions
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Learner Course Reminders - WhatsApp course completion reminders
        Schema::create('learner_course_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->string('phone_number');
            $table->string('reminder_type')->default('completion'); // completion, deadline, milestone
            $table->text('message')->nullable();
            $table->timestamp('scheduled_at');
            $table->timestamp('sent_at')->nullable();
            $table->string('status')->default('pending'); // pending, sent, failed, cancelled
            $table->text('response')->nullable(); // WhatsApp API response
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learner_course_reminders');
        Schema::dropIfExists('ai_lecturers');
        Schema::dropIfExists('learner_points');
        Schema::dropIfExists('learner_badges');
        Schema::dropIfExists('gamification_badges');
        Schema::dropIfExists('assessment_attempts');
        Schema::dropIfExists('learner_progress');
    }
};
