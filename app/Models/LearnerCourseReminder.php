<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LearnerCourseReminder extends Model
{
    protected $fillable = [
        'user_id',
        'course_id',
        'phone_number',
        'reminder_type',
        'message',
        'scheduled_at',
        'sent_at',
        'status',
        'response',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function markAsSent(string $response = null): void
    {
        $this->status = 'sent';
        $this->sent_at = now();
        $this->response = $response;
        $this->save();
    }

    public function markAsFailed(string $error): void
    {
        $this->status = 'failed';
        $this->response = $error;
        $this->save();
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending')
            ->where('scheduled_at', '<=', now());
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'pending');
    }
}
