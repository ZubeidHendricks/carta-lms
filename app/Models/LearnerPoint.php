<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LearnerPoint extends Model
{
    protected $fillable = [
        'user_id',
        'points',
        'source',
        'source_id',
        'source_type',
        'description',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function award(
        int $userId, 
        int $points, 
        string $source, 
        ?int $sourceId = null, 
        ?string $sourceType = null,
        ?string $description = null
    ): self {
        return self::create([
            'user_id' => $userId,
            'points' => $points,
            'source' => $source,
            'source_id' => $sourceId,
            'source_type' => $sourceType,
            'description' => $description ?? ucfirst(str_replace('_', ' ', $source)),
        ]);
    }

    public static function getUserTotal(int $userId): int
    {
        return self::where('user_id', $userId)->sum('points');
    }
}
