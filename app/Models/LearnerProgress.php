<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LearnerProgress extends Model
{
    protected $table = 'learner_progress';

    protected $fillable = [
        'user_id',
        'course_id',
        'completion_percentage',
        'lessons_completed',
        'total_lessons',
        'time_spent_minutes',
        'last_accessed_at',
        'completed_at',
    ];

    protected $casts = [
        'last_accessed_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function updateProgress(int $lessonsCompleted, int $totalLessons, int $timeSpent = 0): void
    {
        $this->lessons_completed = $lessonsCompleted;
        $this->total_lessons = $totalLessons;
        $this->completion_percentage = $totalLessons > 0 
            ? round(($lessonsCompleted / $totalLessons) * 100) 
            : 0;
        $this->time_spent_minutes += $timeSpent;
        $this->last_accessed_at = now();

        if ($this->completion_percentage >= 100 && !$this->completed_at) {
            $this->completed_at = now();
        }

        $this->save();
    }

    public function isCompleted(): bool
    {
        return $this->completion_percentage >= 100;
    }
}
