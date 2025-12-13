# 🎭 Tavus Persona Management Guide

## Overview

Personas in Tavus allow you to define the personality, behavior, and interaction style of AI conversations. This guide covers everything you need to know about creating and managing personas in your LMS.

## What is a Persona?

A persona is an AI personality configuration that determines:
- **Tone** - How the AI speaks (professional, friendly, encouraging)
- **Style** - Communication approach (educational, conversational, motivational)
- **Context** - Background knowledge and expertise
- **Behavior** - How the AI responds to different situations

## Database Schema

### tavus_personas Table
```sql
- id                  - Primary key
- user_id             - Owner of the persona (NULL for global)
- persona_id          - Tavus persona identifier
- persona_name        - Display name
- system_prompt       - AI system instructions
- context             - Background context
- layers              - Tavus persona layers (JSON)
- default_replica_id  - Default replica to use
- properties          - Additional properties (JSON)
- metadata            - Tavus API response (JSON)
- is_active           - Active status
- timestamps          - created_at, updated_at
```

## API Endpoints

### List All Personas
```http
GET /api/tavus/personas
Authorization: Bearer {token}
```

**Query Parameters:**
- `active` - Filter by active status (boolean)

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "persona_id": "p_abc123",
      "persona_name": "Course Instructor",
      "system_prompt": "You are a helpful instructor...",
      "is_active": true
    }
  ]
}
```

### Get Single Persona
```http
GET /api/tavus/personas/{id}
Authorization: Bearer {token}
```

### Create Custom Persona
```http
POST /api/tavus/personas
Authorization: Bearer {token}
Content-Type: application/json

{
  "persona_name": "Math Tutor",
  "system_prompt": "You are an expert math tutor who explains concepts clearly.",
  "context": "University-level mathematics instruction",
  "layers": {
    "knowledge_base": ["algebra", "calculus", "geometry"],
    "teaching_style": "step-by-step"
  },
  "properties": {
    "tone": "patient",
    "expertise_level": "expert"
  }
}
```

### Update Persona
```http
PUT /api/tavus/personas/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "persona_name": "Advanced Math Tutor",
  "system_prompt": "Updated prompt...",
  "is_active": true
}
```

### Delete Persona
```http
DELETE /api/tavus/personas/{id}
Authorization: Bearer {token}
```

### Toggle Active Status
```http
POST /api/tavus/personas/{id}/toggle
Authorization: Bearer {token}
```

### Get Persona Configuration
```http
GET /api/tavus/personas/{id}/configuration
Authorization: Bearer {token}
```

Returns the complete configuration for Tavus API usage.

## Preset Personas

### List Available Presets
```http
GET /api/tavus/personas/presets
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "type": "instructor",
      "name": "Course Instructor",
      "description": "Professional educator focused on teaching"
    },
    {
      "type": "tutor",
      "name": "Personal Tutor", 
      "description": "Friendly tutor providing personalized help"
    },
    {
      "type": "mentor",
      "name": "Career Mentor",
      "description": "Experienced mentor for career guidance"
    },
    {
      "type": "support",
      "name": "Student Support",
      "description": "Helpful assistant for student queries"
    }
  ]
}
```

### Create Preset Persona
```http
POST /api/tavus/personas/presets
Authorization: Bearer {token}
Content-Type: application/json

{
  "type": "instructor"
}
```

## Preset Types

### 1. Instructor
**Best for:** Course teaching, lectures, concept explanations

```json
{
  "persona_name": "Course Instructor",
  "system_prompt": "You are a knowledgeable and patient course instructor. Help students understand complex topics, answer questions clearly, and provide encouragement.",
  "context": "Educational environment focused on student learning",
  "properties": {
    "tone": "professional",
    "style": "educational",
    "expertise_level": "expert"
  }
}
```

**Use cases:**
- Course introduction videos
- Lesson explanations
- Concept clarifications
- Study tips

### 2. Tutor
**Best for:** One-on-one help, personalized explanations

```json
{
  "persona_name": "Personal Tutor",
  "system_prompt": "You are a friendly tutor who provides personalized help. Break down difficult concepts, give examples, and adapt explanations to the student's level.",
  "context": "One-on-one tutoring session",
  "properties": {
    "tone": "friendly",
    "style": "conversational",
    "patience_level": "high"
  }
}
```

**Use cases:**
- Assignment help
- Concept reinforcement
- Practice problems
- Study sessions

### 3. Mentor
**Best for:** Career guidance, professional development

```json
{
  "persona_name": "Career Mentor",
  "system_prompt": "You are an experienced mentor providing career guidance. Share insights, offer constructive feedback, and inspire confidence.",
  "context": "Professional mentorship and career development",
  "properties": {
    "tone": "encouraging",
    "style": "motivational",
    "focus": "career_development"
  }
}
```

**Use cases:**
- Career advice
- Project feedback
- Goal setting
- Skill development

### 4. Support
**Best for:** General help, troubleshooting, FAQs

```json
{
  "persona_name": "Student Support",
  "system_prompt": "You are a helpful support assistant. Address student questions, resolve concerns, and guide them to resources.",
  "context": "Student support and assistance",
  "properties": {
    "tone": "helpful",
    "style": "supportive",
    "response_time": "immediate"
  }
}
```

**Use cases:**
- Course navigation help
- Technical support
- General questions
- Resource guidance

## PHP Usage Examples

### Create a Custom Persona
```php
use Modules\Tavus\Models\TavusPersona;

$persona = TavusPersona::create([
    'user_id' => auth()->id(),
    'persona_id' => 'p_' . uniqid(),
    'persona_name' => 'Programming Mentor',
    'system_prompt' => 'You are an experienced software developer who mentors junior programmers.',
    'context' => 'Teaching programming best practices and code review',
    'properties' => [
        'languages' => ['PHP', 'JavaScript', 'Python'],
        'focus_areas' => ['clean code', 'testing', 'architecture']
    ],
    'is_active' => true,
]);
```

### Create a Preset Persona
```php
$persona = TavusPersona::createPreset('tutor', auth()->id());
```

### Use Persona in Conversation
```php
use Modules\Tavus\Services\TavusService;

$tavus = app(TavusService::class);
$persona = TavusPersona::find(1);

$conversation = $tavus->createConversation([
    'replica_id' => 'r_abc123',
    'persona_id' => $persona->persona_id,
    'conversational_context' => $persona->context,
    'custom_greeting' => $persona->custom_greeting ?? 'Hello!',
], auth()->id());
```

### Get All Active Personas
```php
$personas = TavusPersona::active()
    ->byUser(auth()->id())
    ->get();
```

### Toggle Persona Status
```php
$persona = TavusPersona::find(1);
$persona->update(['is_active' => !$persona->is_active]);
```

## Advanced Configuration

### Persona Layers
Layers allow you to define multiple aspects of the persona:

```json
{
  "layers": {
    "knowledge_base": {
      "domains": ["mathematics", "physics"],
      "level": "university"
    },
    "personality": {
      "traits": ["patient", "encouraging", "detail-oriented"],
      "communication_style": "socratic_method"
    },
    "constraints": {
      "max_response_length": 500,
      "language_complexity": "intermediate"
    },
    "behavior": {
      "on_confusion": "ask_clarifying_questions",
      "on_success": "provide_encouragement",
      "on_error": "gentle_correction"
    }
  }
}
```

### System Prompt Best Practices

**Good Example:**
```
You are Dr. Sarah Chen, a university professor with 15 years of experience teaching Computer Science. You specialize in making complex algorithms accessible to beginners. 

Your teaching style is:
- Start with real-world examples
- Use analogies from everyday life
- Break down problems into smaller steps
- Encourage questions and critical thinking
- Provide constructive feedback

When students struggle, offer hints rather than complete solutions. When they succeed, acknowledge their progress and suggest next challenges.
```

**What to Avoid:**
```
You are a teacher. Help students.
```

### Context Guidelines

**Good Example:**
```
You are teaching an introductory web development course to adult learners transitioning into tech careers. Students have basic computer literacy but no prior programming experience. The course covers HTML, CSS, and JavaScript over 12 weeks. You emphasize practical skills and real-world applications.
```

## Use Cases by Course Type

### Programming Courses
```php
TavusPersona::create([
    'persona_name' => 'Code Mentor',
    'system_prompt' => 'You are a senior developer who reviews code and teaches best practices...',
    'properties' => [
        'languages' => ['JavaScript', 'Python', 'PHP'],
        'specialties' => ['code_review', 'debugging', 'architecture']
    ]
]);
```

### Business Courses
```php
TavusPersona::create([
    'persona_name' => 'Business Coach',
    'system_prompt' => 'You are an MBA-educated business consultant...',
    'properties' => [
        'expertise' => ['strategy', 'marketing', 'finance'],
        'approach' => 'case_study_based'
    ]
]);
```

### Language Courses
```php
TavusPersona::create([
    'persona_name' => 'Language Tutor',
    'system_prompt' => 'You are a native Spanish speaker teaching conversational Spanish...',
    'properties' => [
        'native_language' => 'Spanish',
        'teaching_method' => 'immersive',
        'correction_style' => 'gentle'
    ]
]);
```

### Creative Courses
```php
TavusPersona::create([
    'persona_name' => 'Art Instructor',
    'system_prompt' => 'You are a professional artist teaching creative techniques...',
    'properties' => [
        'mediums' => ['digital', 'watercolor', 'oil'],
        'style' => 'encouraging_experimentation'
    ]
]);
```

## Integration with Conversations

### Create Conversation with Persona
```php
POST /api/tavus/conversations
{
  "replica_id": "r_abc123",
  "persona_id": "p_xyz789",
  "conversation_name": "Office Hours"
}
```

The persona will automatically configure:
- System prompt
- Context
- Behavior layers
- Response style

## Testing Your Persona

### 1. Test Basic Responses
```bash
curl -X POST /api/tavus/conversations \
  -H "Authorization: Bearer {token}" \
  -d '{
    "replica_id": "r_abc123",
    "persona_id": "p_xyz789"
  }'
```

### 2. Evaluate Tone and Style
Ask the persona different types of questions:
- Simple questions (tests clarity)
- Complex questions (tests depth)
- Confused questions (tests patience)
- Incorrect assumptions (tests correction style)

### 3. Check Consistency
The persona should maintain consistent:
- Tone across interactions
- Knowledge level
- Teaching approach
- Response format

## Best Practices

1. **Be Specific** - Detailed system prompts work better than vague ones
2. **Define Boundaries** - Specify what the persona should and shouldn't do
3. **Test Thoroughly** - Try edge cases and difficult scenarios
4. **Iterate** - Refine based on actual conversations
5. **Consider Context** - Match persona to course type and student level
6. **Use Presets** - Start with presets, then customize
7. **Version Control** - Keep track of prompt changes
8. **Student Feedback** - Adjust based on how students respond

## Troubleshooting

### Persona responses are inconsistent
- Make system prompt more specific
- Add more context
- Define behavior guidelines

### Persona is too formal/informal
- Adjust tone in properties
- Update system prompt
- Add example interactions

### Persona doesn't stay in character
- Strengthen the role definition
- Add constraints to layers
- Include "do not" instructions

## Analytics

Track persona performance:

```php
// Get usage stats
$persona = TavusPersona::find(1);
$conversationCount = $persona->conversations()->count();
$averageDuration = $persona->conversations()->avg('duration');

// Get student satisfaction
$ratings = $persona->conversations()
    ->whereNotNull('rating')
    ->avg('rating');
```

## Next Steps

1. Create your first preset persona
2. Test it in a conversation
3. Customize based on feedback
4. Create course-specific personas
5. Monitor and refine

---

**Related Documentation:**
- TAVUS_INTEGRATION.md - Main integration guide
- Modules/Tavus/README.md - Full module docs
- TAVUS_QUICKREF.md - API reference
