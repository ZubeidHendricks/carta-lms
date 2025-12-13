<?php

namespace App\Services\Gamification;

use App\Models\{User, Course, AssessmentAttempt, LearnerPoint};

class AssessmentService
{
    public function __construct(private GamificationService $gamificationService)
    {
    }

    public function startAttempt(User $user, Course $course, ?int $quizId = null, string $assessmentType = 'quiz', int $maxScore = 100): AssessmentAttempt
    {
        $attemptNumber = AssessmentAttempt::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('quiz_id', $quizId)
            ->max('attempt_number') + 1;

        return AssessmentAttempt::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'quiz_id' => $quizId,
            'assessment_type' => $assessmentType,
            'attempt_number' => $attemptNumber,
            'max_score' => $maxScore,
            'status' => 'in_progress',
            'started_at' => now(),
        ]);
    }

    public function completeAttempt(AssessmentAttempt $attempt, int $score): AssessmentAttempt
    {
        $attempt->complete($score);

        // Award points if passed
        if ($attempt->isPassed()) {
            $points = $this->calculatePoints($attempt->percentage);
            $this->gamificationService->awardPoints(
                $attempt->user,
                $points,
                'quiz_passed',
                $attempt->id,
                'assessment',
                "Scored {$attempt->percentage}% on {$attempt->assessment_type}"
            );
        }

        return $attempt;
    }

    private function calculatePoints(float $percentage): int
    {
        return match(true) {
            $percentage >= 95 => 50,
            $percentage >= 90 => 40,
            $percentage >= 80 => 30,
            $percentage >= 70 => 20,
            default => 10,
        };
    }

    public function getUserAttempts(User $user, ?int $courseId = null): array
    {
        $query = AssessmentAttempt::where('user_id', $user->id)
            ->with('course');

        if ($courseId) {
            $query->where('course_id', $courseId);
        }

        return $query->orderByDesc('created_at')->get()->toArray();
    }

    public function getAttemptStats(User $user): array
    {
        $attempts = AssessmentAttempt::where('user_id', $user->id)->get();

        return [
            'total_attempts' => $attempts->count(),
            'passed_attempts' => $attempts->filter(fn($a) => $a->isPassed())->count(),
            'average_score' => $attempts->avg('percentage') ?? 0,
            'highest_score' => $attempts->max('percentage') ?? 0,
            'total_time_spent' => $attempts->sum('time_taken_minutes'),
        ];
    }
}
