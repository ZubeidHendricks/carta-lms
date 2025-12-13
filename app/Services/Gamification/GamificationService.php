<?php

namespace App\Services\Gamification;

use App\Models\{User, LearnerProgress, LearnerPoint, GamificationBadge, LearnerBadge};

class GamificationService
{
    public function trackProgress(User $user, int $courseId, int $lessonsCompleted, int $totalLessons, int $timeSpent = 0): LearnerProgress
    {
        $progress = LearnerProgress::firstOrNew([
            'user_id' => $user->id,
            'course_id' => $courseId,
        ]);

        $wasCompleted = $progress->isCompleted();
        $progress->updateProgress($lessonsCompleted, $totalLessons, $timeSpent);

        // Award points for progress
        if (!$wasCompleted && $progress->isCompleted()) {
            $this->awardCourseCompletionPoints($user, $courseId);
            $this->checkAndAwardBadges($user);
        }

        return $progress;
    }

    public function awardPoints(User $user, int $points, string $source, ?int $sourceId = null, ?string $sourceType = null, ?string $description = null): LearnerPoint
    {
        $point = LearnerPoint::award($user->id, $points, $source, $sourceId, $sourceType, $description);
        
        // Check if user earned any badges based on points
        $this->checkAndAwardBadges($user);
        
        return $point;
    }

    private function awardCourseCompletionPoints(User $user, int $courseId): void
    {
        LearnerPoint::award(
            $user->id,
            100,
            'course_completion',
            $courseId,
            'course',
            'Completed a course'
        );
    }

    public function checkAndAwardBadges(User $user): array
    {
        $newBadges = [];
        $badges = GamificationBadge::where('is_active', true)->get();

        foreach ($badges as $badge) {
            // Check if user already has this badge
            if (LearnerBadge::where('user_id', $user->id)->where('badge_id', $badge->id)->exists()) {
                continue;
            }

            // Check if user meets criteria
            if ($badge->checkCriteria($user)) {
                $learnerBadge = LearnerBadge::create([
                    'user_id' => $user->id,
                    'badge_id' => $badge->id,
                    'earned_at' => now(),
                    'metadata' => ['auto_awarded' => true],
                ]);

                // Award points for earning badge
                LearnerPoint::award(
                    $user->id,
                    $badge->points_reward,
                    'badge_earned',
                    $badge->id,
                    'badge',
                    "Earned badge: {$badge->name}"
                );

                $newBadges[] = $learnerBadge;
            }
        }

        return $newBadges;
    }

    public function getUserStats(User $user): array
    {
        return [
            'total_points' => LearnerPoint::getUserTotal($user->id),
            'badges_earned' => LearnerBadge::where('user_id', $user->id)->count(),
            'courses_completed' => LearnerProgress::where('user_id', $user->id)
                ->where('completion_percentage', 100)
                ->count(),
            'courses_in_progress' => LearnerProgress::where('user_id', $user->id)
                ->where('completion_percentage', '>', 0)
                ->where('completion_percentage', '<', 100)
                ->count(),
            'total_time_spent' => LearnerProgress::where('user_id', $user->id)
                ->sum('time_spent_minutes'),
        ];
    }

    public function getLeaderboard(int $limit = 10): array
    {
        return User::select('users.id', 'users.name', 'users.email')
            ->selectRaw('COALESCE(SUM(learner_points.points), 0) as total_points')
            ->selectRaw('COUNT(DISTINCT learner_badges.id) as badges_count')
            ->leftJoin('learner_points', 'users.id', '=', 'learner_points.user_id')
            ->leftJoin('learner_badges', 'users.id', '=', 'learner_badges.user_id')
            ->where('users.role', 'student')
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderByDesc('total_points')
            ->limit($limit)
            ->get()
            ->toArray();
    }
}
