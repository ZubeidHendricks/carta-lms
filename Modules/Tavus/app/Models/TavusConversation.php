<?php

namespace Modules\Tavus\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class TavusConversation extends Model
{
    protected $fillable = [
        'user_id',
        'conversation_id',
        'conversation_name',
        'replica_id',
        'persona_id',
        'conversational_context',
        'custom_greeting',
        'status',
        'duration',
        'properties',
        'started_at',
        'ended_at',
    ];

    protected $casts = [
        'properties' => 'array',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function replica(): BelongsTo
    {
        return $this->belongsTo(TavusReplica::class, 'replica_id', 'replica_id');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function hasEnded(): bool
    {
        return $this->status === 'ended';
    }

    public function hasFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function getDuration(): int
    {
        if ($this->duration) {
            return $this->duration;
        }

        if ($this->started_at && $this->ended_at) {
            return $this->ended_at->diffInSeconds($this->started_at);
        }

        return 0;
    }
}
