<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GamificationBadge extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'category',
        'criteria_type',
        'criteria_data',
        'points_reward',
        'is_active',
    ];

    protected $casts = [
        'criteria_data' => 'array',
        'is_active' => 'boolean',
    ];

    public function learnerBadges(): HasMany
    {
        return $this->hasMany(LearnerBadge::class, 'badge_id');
    }

    public function checkCriteria(User $user): bool
    {
        return match($this->criteria_type) {
            'course_completion' => $this->checkCourseCompletion($user),
            'points_earned' => $this->checkPointsEarned($user),
            'streak' => $this->checkStreak($user),
            'assessment_score' => $this->checkAssessmentScore($user),
            'lesson_completion' => $this->checkLessonCompletion($user),
            default => false,
        };
    }

    private function checkCourseCompletion(User $user): bool
    {
        $required = $this->criteria_data['courses_required'] ?? 1;
        $completed = LearnerProgress::where('user_id', $user->id)
            ->where('completion_percentage', 100)
            ->count();
        return $completed >= $required;
    }

    private function checkPointsEarned(User $user): bool
    {
        $required = $this->criteria_data['points_required'] ?? 100;
        $earned = LearnerPoint::where('user_id', $user->id)->sum('points');
        return $earned >= $required;
    }

    private function checkAssessmentScore(User $user): bool
    {
        $minScore = $this->criteria_data['min_score'] ?? 90;
        $count = $this->criteria_data['count'] ?? 1;
        $passed = AssessmentAttempt::where('user_id', $user->id)
            ->where('percentage', '>=', $minScore)
            ->count();
        return $passed >= $count;
    }

    private function checkLessonCompletion(User $user): bool
    {
        $required = $this->criteria_data['lessons_required'] ?? 1;
        $completed = LearnerProgress::where('user_id', $user->id)
            ->sum('lessons_completed');
        return $completed >= $required;
    }

    private function checkStreak(User $user): bool
    {
        // Implement streak logic based on login history
        return false; // Placeholder
    }
}
