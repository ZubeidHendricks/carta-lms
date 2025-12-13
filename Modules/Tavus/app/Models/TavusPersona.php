<?php

namespace Modules\Tavus\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;

class TavusPersona extends Model
{
    protected $fillable = [
        'user_id',
        'persona_id',
        'persona_name',
        'system_prompt',
        'context',
        'layers',
        'default_replica_id',
        'properties',
        'metadata',
        'is_active',
    ];

    protected $casts = [
        'layers' => 'array',
        'properties' => 'array',
        'metadata' => 'array',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(TavusConversation::class, 'persona_id', 'persona_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get the full persona configuration for Tavus API
     */
    public function getApiConfiguration(): array
    {
        $config = [
            'persona_id' => $this->persona_id,
            'persona_name' => $this->persona_name,
        ];

        if ($this->system_prompt) {
            $config['system_prompt'] = $this->system_prompt;
        }

        if ($this->context) {
            $config['context'] = $this->context;
        }

        if ($this->layers) {
            $config['layers'] = $this->layers;
        }

        if ($this->default_replica_id) {
            $config['default_replica_id'] = $this->default_replica_id;
        }

        if ($this->properties) {
            $config['properties'] = $this->properties;
        }

        return $config;
    }

    /**
     * Create a preset persona for common use cases
     */
    public static function createPreset(string $type, ?int $userId = null): self
    {
        $presets = [
            'instructor' => [
                'persona_name' => 'Course Instructor',
                'system_prompt' => 'You are a knowledgeable and patient course instructor. Help students understand complex topics, answer questions clearly, and provide encouragement.',
                'context' => 'Educational environment focused on student learning and engagement.',
                'properties' => [
                    'tone' => 'professional',
                    'style' => 'educational',
                    'expertise_level' => 'expert',
                ],
            ],
            'tutor' => [
                'persona_name' => 'Personal Tutor',
                'system_prompt' => 'You are a friendly tutor who provides personalized help. Break down difficult concepts, give examples, and adapt explanations to the student\'s level.',
                'context' => 'One-on-one tutoring session with focus on individual student needs.',
                'properties' => [
                    'tone' => 'friendly',
                    'style' => 'conversational',
                    'patience_level' => 'high',
                ],
            ],
            'mentor' => [
                'persona_name' => 'Career Mentor',
                'system_prompt' => 'You are an experienced mentor providing career guidance and professional advice. Share insights, offer constructive feedback, and inspire confidence.',
                'context' => 'Professional mentorship focused on career development and skill building.',
                'properties' => [
                    'tone' => 'encouraging',
                    'style' => 'motivational',
                    'focus' => 'career_development',
                ],
            ],
            'support' => [
                'persona_name' => 'Student Support',
                'system_prompt' => 'You are a helpful support assistant. Address student questions, resolve concerns, and guide them to appropriate resources.',
                'context' => 'Student support and assistance with course-related queries.',
                'properties' => [
                    'tone' => 'helpful',
                    'style' => 'supportive',
                    'response_time' => 'immediate',
                ],
            ],
        ];

        $preset = $presets[$type] ?? $presets['instructor'];
        $preset['user_id'] = $userId;
        $preset['persona_id'] = 'preset_' . $type . '_' . uniqid();

        return self::create($preset);
    }
}
