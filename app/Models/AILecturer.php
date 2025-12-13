<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AILecturer extends Model
{
    protected $table = 'ai_lecturers';

    protected $fillable = [
        'name',
        'avatar',
        'voice_id',
        'personality_traits',
        'teaching_style',
        'expertise_area',
        'prompt_template',
        'is_active',
    ];

    protected $casts = [
        'prompt_template' => 'array',
        'is_active' => 'boolean',
    ];

    public function generateResponse(string $question, array $context = []): array
    {
        $systemPrompt = $this->prompt_template['system'] ?? 'You are a helpful AI instructor.';
        $styleGuide = $this->prompt_template['style'] ?? '';

        return [
            'system_prompt' => $systemPrompt,
            'style_guide' => $styleGuide,
            'personality' => $this->personality_traits,
            'teaching_style' => $this->teaching_style,
            'question' => $question,
            'context' => $context,
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
