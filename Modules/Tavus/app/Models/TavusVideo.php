<?php

namespace Modules\Tavus\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class TavusVideo extends Model
{
    protected $fillable = [
        'user_id',
        'course_id',
        'video_id',
        'video_name',
        'replica_id',
        'script',
        'background_source_url',
        'status',
        'download_url',
        'hosted_url',
        'local_path',
        'duration',
        'variables',
        'metadata',
        'completed_at',
    ];

    protected $casts = [
        'variables' => 'array',
        'metadata' => 'array',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Course::class);
    }

    public function replica(): BelongsTo
    {
        return $this->belongsTo(TavusReplica::class, 'replica_id', 'replica_id');
    }

    public function isReady(): bool
    {
        return $this->status === 'ready';
    }

    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    public function hasFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function getVideoUrl(): ?string
    {
        if ($this->local_path) {
            return asset('storage/' . $this->local_path);
        }
        
        return $this->hosted_url ?? $this->download_url;
    }
}
