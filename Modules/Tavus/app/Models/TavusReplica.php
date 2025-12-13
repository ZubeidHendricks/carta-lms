<?php

namespace Modules\Tavus\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;

class TavusReplica extends Model
{
    protected $fillable = [
        'user_id',
        'replica_id',
        'replica_name',
        'training_video_url',
        'status',
        'callback_url',
        'metadata',
        'trained_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'trained_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function videos(): HasMany
    {
        return $this->hasMany(TavusVideo::class, 'replica_id', 'replica_id');
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(TavusConversation::class, 'replica_id', 'replica_id');
    }

    public function isReady(): bool
    {
        return $this->status === 'ready';
    }

    public function isTraining(): bool
    {
        return $this->status === 'training';
    }

    public function hasFailed(): bool
    {
        return $this->status === 'failed';
    }
}
