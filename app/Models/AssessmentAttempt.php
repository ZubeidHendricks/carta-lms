<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentAttempt extends Model
{
    protected $fillable = [
        'user_id',
        'course_id',
        'quiz_id',
        'assessment_type',
        'attempt_number',
        'score',
        'max_score',
        'percentage',
        'time_taken_minutes',
        'status',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
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

    public function calculatePercentage(): void
    {
        if ($this->max_score > 0) {
            $this->percentage = round(($this->score / $this->max_score) * 100, 2);
        }
    }

    public function isPassed(int $passingScore = 70): bool
    {
        return $this->percentage >= $passingScore;
    }

    public function complete(int $score): void
    {
        $this->score = $score;
        $this->calculatePercentage();
        $this->status = 'completed';
        $this->completed_at = now();
        $this->time_taken_minutes = now()->diffInMinutes($this->started_at);
        $this->save();
    }
}
