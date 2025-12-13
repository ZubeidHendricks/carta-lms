# AI Tutor RAG System - 404 Fix

## Problem
The AI Tutor page was showing a 404 error when trying to submit questions because the API endpoint `/api/ai-lecturers/ask` was missing.

## Solution Implemented

### 1. Created AILecturerController
**File:** `app/Http/Controllers/AI/AILecturerController.php`

This controller handles AI tutor questions with a `ask()` method that:
- Validates the incoming request (question, lecturer_id, context)
- Retrieves the selected AI lecturer
- Returns a response (placeholder for now, ready for RAG integration)

### 2. Added API Route
**File:** `routes/web.php`

Added the following route:
```php
Route::middleware(['auth'])->prefix('api')->group(function () {
    Route::post('ai-lecturers/ask', [\App\Http\Controllers\AI\AILecturerController::class, 'ask'])
        ->name('api.ai-lecturers.ask');
});
```

## How It Works

1. **Frontend Component:** `resources/js/components/gamification/ai-chat-interface.tsx`
   - User types a question
   - Component sends POST request to `/api/ai-lecturers/ask`
   - Displays the response in the chat interface

2. **Backend Route:** `routes/web.php`
   - Receives the POST request
   - Requires authentication
   - Routes to AILecturerController

3. **Controller:** `app/Http/Controllers/AI/AILecturerController.php`
   - Validates request data
   - Fetches lecturer information
   - Generates response (currently placeholder)
   - Returns JSON response

## Next Steps: Implementing Full RAG System

To implement a complete RAG (Retrieval Augmented Generation) system, update the `generateResponse()` method in `AILecturerController.php`:

### Option 1: OpenAI Integration
```php
use OpenAI\Laravel\Facades\OpenAI;

private function generateResponse(string $question, AILecturer $lecturer, ?array $context): string
{
    $systemPrompt = $lecturer->system_prompt ?? "You are a helpful AI tutor.";
    
    $response = OpenAI::chat()->create([
        'model' => 'gpt-4',
        'messages' => [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $question],
        ],
    ]);
    
    return $response->choices[0]->message->content;
}
```

### Option 2: Custom RAG with Vector Database
```php
private function generateResponse(string $question, AILecturer $lecturer, ?array $context): string
{
    // 1. Retrieve relevant course content from vector database
    $relevantContent = $this->retrieveRelevantContent($question, $context);
    
    // 2. Build context-aware prompt
    $prompt = $this->buildPrompt($question, $relevantContent, $lecturer);
    
    // 3. Generate response using LLM
    $response = $this->callLLM($prompt);
    
    return $response;
}
```

### Option 3: Tavus Integration
Since you have Tavus module, you could integrate conversational AI:
```php
use Modules\Tavus\Services\TavusService;

private function generateResponse(string $question, AILecturer $lecturer, ?array $context): string
{
    $tavusService = app(TavusService::class);
    
    // Start or continue conversation
    $conversation = $tavusService->createConversation([
        'persona_id' => $lecturer->tavus_persona_id,
        'context' => $context,
    ]);
    
    $response = $tavusService->sendMessage($conversation->id, $question);
    
    return $response->text;
}
```

## Testing

Once deployed, test the AI Tutor:

1. Navigate to: `http://165.227.113.197/student/ai-tutor`
2. Login with student credentials
3. Select an AI lecturer from the sidebar
4. Type a question in the chat interface
5. Submit and verify response appears

## Deployment

The changes are ready. To deploy:

```bash
./deploy-to-do.sh
```

Or if already deployed, SSH into the server and run:
```bash
cd /opt/mentor-lms
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
```

## Files Modified

1. ✅ `routes/web.php` - Added API route
2. ✅ `app/Http/Controllers/AI/AILecturerController.php` - Created controller

## Files Already Present

- ✅ `app/Models/AILecturer.php` - Model for AI lecturers
- ✅ `app/Http/Controllers/Student/LearningDashboardController.php` - Page controller with aiTutor() method
- ✅ `resources/js/pages/student/ai-tutor/index.tsx` - Frontend page
- ✅ `resources/js/components/gamification/ai-chat-interface.tsx` - Chat component
- ✅ Database table: `ai_lecturers` - Stores AI lecturer data

## Status

✅ **404 Error Fixed** - The route now exists and will handle requests
⏳ **RAG System** - Placeholder response implemented, ready for full RAG integration
