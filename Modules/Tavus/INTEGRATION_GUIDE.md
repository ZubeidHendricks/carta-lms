# Tavus Integration Guide for Mentor LMS

## Quick Start

### 1. Get Your Tavus API Key

1. Visit [Tavus Platform](https://platform.tavus.io/)
2. Sign up or log in to your account
3. Navigate to Settings → API Keys
4. Generate a new API key
5. Copy the API key

### 2. Configure Your Environment

Add to your `.env` file:
```env
TAVUS_API_KEY=your_api_key_here
```

### 3. Run Database Migrations

```bash
php artisan migrate
```

### 4. Test the Integration

```bash
# Check if module is loaded
php artisan module:list

# You should see Tavus listed as enabled
```

## Use Cases

### 1. Personalized Course Welcome Videos

When a student enrolls in a course, automatically generate a personalized welcome video from the instructor.

```php
// In your enrollment controller
use Modules\Tavus\Services\TavusService;

public function enrollStudent($courseId, $studentId)
{
    // ... enrollment logic ...
    
    $tavus = app(TavusService::class);
    $course = Course::find($courseId);
    $student = User::find($studentId);
    
    // Generate personalized welcome video
    $video = $tavus->generateVideo([
        'video_name' => "Welcome to {$course->title}",
        'replica_id' => $course->instructor->tavus_replica_id,
        'script' => "Hi {{student_name}}! Welcome to {{course_name}}. I'm {{instructor_name}}, and I'm excited to be your instructor on this learning journey.",
        'variables' => [
            'student_name' => $student->name,
            'course_name' => $course->title,
            'instructor_name' => $course->instructor->name,
        ],
    ], $course->instructor->id, $course->id);
    
    // Video will be processed asynchronously
    // Notify student when ready via webhook
}
```

### 2. Assignment Video Feedback

Provide personalized video feedback on student assignments.

```php
public function provideVideoFeedback($assignmentSubmission)
{
    $tavus = app(TavusService::class);
    $instructor = $assignmentSubmission->assignment->course->instructor;
    
    $feedback = $tavus->generateVideo([
        'video_name' => "Feedback for {$assignmentSubmission->student->name}",
        'replica_id' => $instructor->tavus_replica_id,
        'script' => "Hi {{student_name}}, I've reviewed your submission for {{assignment_name}}. {{feedback_text}}",
        'variables' => [
            'student_name' => $assignmentSubmission->student->name,
            'assignment_name' => $assignmentSubmission->assignment->title,
            'feedback_text' => $assignmentSubmission->instructor_feedback,
        ],
    ], $instructor->id);
    
    $assignmentSubmission->update([
        'video_feedback_id' => $feedback->id
    ]);
}
```

### 3. Interactive Virtual Office Hours

Create a conversational AI for 24/7 student support.

```php
public function startVirtualOfficeHours($courseId)
{
    $tavus = app(TavusService::class);
    $course = Course::find($courseId);
    
    $conversation = $tavus->createConversation([
        'conversation_name' => "Office Hours - {$course->title}",
        'replica_id' => $course->instructor->tavus_replica_id,
        'conversational_context' => "You are {$course->instructor->name}, instructor for {$course->title}. Help students with course-related questions, provide clarification on topics, and offer encouragement.",
        'custom_greeting' => "Hello! I'm here to help you with any questions about {$course->title}. What can I help you with today?",
    ], $course->instructor->id);
    
    return $conversation;
}
```

### 4. Course Announcement Videos

Generate announcement videos for course updates.

```php
public function createAnnouncement($courseId, $announcementText)
{
    $tavus = app(TavusService::class);
    $course = Course::find($courseId);
    
    $video = $tavus->generateVideo([
        'video_name' => "Course Announcement",
        'replica_id' => $course->instructor->tavus_replica_id,
        'script' => $announcementText,
    ], $course->instructor->id, $course->id);
    
    // Broadcast to all enrolled students
    $course->announcements()->create([
        'title' => 'New Video Announcement',
        'video_id' => $video->id,
    ]);
}
```

## Frontend Integration

### React Component for Video Display

Create a component at `resources/js/Components/TavusVideo.tsx`:

```typescript
import React, { useEffect, useState } from 'react';
import axios from 'axios';

interface TavusVideoProps {
    videoId: number;
    onReady?: (url: string) => void;
}

export default function TavusVideo({ videoId, onReady }: TavusVideoProps) {
    const [video, setVideo] = useState<any>(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        loadVideo();
        const interval = setInterval(checkStatus, 5000);
        return () => clearInterval(interval);
    }, [videoId]);

    const loadVideo = async () => {
        try {
            const response = await axios.get(`/api/tavus/videos/${videoId}`);
            setVideo(response.data.data);
            
            if (response.data.data.status === 'ready') {
                setLoading(false);
                onReady?.(response.data.data.hosted_url || response.data.data.download_url);
            }
        } catch (err) {
            setError('Failed to load video');
            setLoading(false);
        }
    };

    const checkStatus = async () => {
        if (video?.status === 'ready') return;
        
        try {
            const response = await axios.post(`/api/tavus/videos/${videoId}/sync`);
            setVideo(response.data.data);
            
            if (response.data.data.status === 'ready') {
                setLoading(false);
                onReady?.(response.data.data.hosted_url || response.data.data.download_url);
            }
        } catch (err) {
            console.error('Failed to sync video status', err);
        }
    };

    if (loading) {
        return (
            <div className="flex items-center justify-center p-8">
                <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
                <p className="ml-4">Generating your personalized video...</p>
            </div>
        );
    }

    if (error) {
        return <div className="text-red-500">{error}</div>;
    }

    if (!video) return null;

    const videoUrl = video.local_path 
        ? `/storage/${video.local_path}`
        : video.hosted_url || video.download_url;

    return (
        <div className="tavus-video-container">
            <video
                controls
                className="w-full rounded-lg shadow-lg"
                src={videoUrl}
            >
                Your browser does not support the video tag.
            </video>
            <div className="mt-2 text-sm text-gray-600">
                Duration: {Math.floor(video.duration / 60)}:{(video.duration % 60).toString().padStart(2, '0')}
            </div>
        </div>
    );
}
```

### React Component for Conversational Interface

```typescript
import React, { useEffect, useRef } from 'react';

interface TavusConversationProps {
    conversationId: string;
    onEnd?: () => void;
}

export default function TavusConversation({ conversationId, onEnd }: TavusConversationProps) {
    const containerRef = useRef<HTMLDivElement>(null);
    const conversationRef = useRef<any>(null);

    useEffect(() => {
        // Load Tavus CVI SDK
        const script = document.createElement('script');
        script.src = 'https://tavusapi.com/cvi-sdk.js';
        script.async = true;
        document.body.appendChild(script);

        script.onload = () => {
            initializeConversation();
        };

        return () => {
            document.body.removeChild(script);
            if (conversationRef.current) {
                conversationRef.current.destroy();
            }
        };
    }, [conversationId]);

    const initializeConversation = () => {
        if (!window.TavusCVI) return;

        conversationRef.current = new window.TavusCVI({
            conversationId: conversationId,
            container: containerRef.current,
            onConversationEnd: () => {
                onEnd?.();
            },
        });
    };

    return (
        <div 
            ref={containerRef}
            className="tavus-conversation-container w-full h-[600px] rounded-lg shadow-lg"
        />
    );
}
```

## Webhook Setup

### 1. Configure Webhook URL

In Tavus Platform, set your webhook URL to:
```
https://yourdomain.com/api/tavus/webhook
```

### 2. Handle Webhook Events

The module automatically handles webhooks. You can add custom logic by listening to events:

```php
// In EventServiceProvider.php
use Modules\Tavus\Models\TavusVideo;

Event::listen(function () {
    TavusVideo::created(function ($video) {
        // Send notification to user
        $video->user->notify(new VideoGenerationStarted($video));
    });
    
    TavusVideo::updated(function ($video) {
        if ($video->wasChanged('status') && $video->status === 'ready') {
            // Notify user video is ready
            $video->user->notify(new VideoReady($video));
        }
    });
});
```

## Best Practices

### 1. Replica Training

```php
// Create instructor replica during onboarding
public function createInstructorReplica(User $instructor, $trainingVideoPath)
{
    $tavus = app(TavusService::class);
    
    $replica = $tavus->createReplica([
        'replica_name' => $instructor->name,
        'train_video_url' => Storage::url($trainingVideoPath),
        'callback_url' => route('tavus.webhook'),
    ], $instructor->id);
    
    $instructor->update([
        'tavus_replica_id' => $replica->replica_id
    ]);
    
    return $replica;
}
```

### 2. Video Generation Queue

For bulk video generation, use Laravel queues:

```php
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class GenerateWelcomeVideos implements ShouldQueue
{
    use Queueable, SerializesModels, InteractsWithQueue;

    public function __construct(
        public Course $course,
        public array $students
    ) {}

    public function handle(TavusService $tavus)
    {
        foreach ($this->students as $student) {
            $tavus->generateVideo([
                'video_name' => "Welcome {$student->name}",
                'replica_id' => $this->course->instructor->tavus_replica_id,
                'script' => "Hi {{name}}, welcome to {{course}}!",
                'variables' => [
                    'name' => $student->name,
                    'course' => $this->course->title,
                ],
            ], $this->course->instructor->id, $this->course->id);
        }
    }
}

// Dispatch the job
GenerateWelcomeVideos::dispatch($course, $enrolledStudents);
```

### 3. Error Handling

```php
try {
    $video = $tavus->generateVideo($data);
} catch (\GuzzleHttp\Exception\ClientException $e) {
    // Handle API errors
    if ($e->getCode() === 401) {
        Log::error('Invalid Tavus API key');
    } elseif ($e->getCode() === 429) {
        Log::warning('Tavus API rate limit exceeded');
        // Retry later
    }
} catch (\Exception $e) {
    Log::error('Failed to generate video', [
        'error' => $e->getMessage(),
        'data' => $data,
    ]);
}
```

## Testing

### Unit Tests

```php
use Tests\TestCase;
use Modules\Tavus\Services\TavusService;
use Modules\Tavus\Models\TavusReplica;

class TavusServiceTest extends TestCase
{
    public function test_create_replica()
    {
        $tavus = app(TavusService::class);
        
        $replica = $tavus->createReplica([
            'replica_name' => 'Test Replica',
            'train_video_url' => 'https://example.com/video.mp4',
        ]);
        
        $this->assertInstanceOf(TavusReplica::class, $replica);
        $this->assertEquals('Test Replica', $replica->replica_name);
    }
}
```

## Performance Optimization

1. **Cache Replica Data**: Cache frequently accessed replica information
2. **Lazy Load Videos**: Only load video URLs when needed
3. **Use CDN**: Configure Tavus to deliver via CDN
4. **Queue Processing**: Process video generation in background jobs

## Monitoring

Monitor your Tavus usage:

```php
// Get usage statistics
$totalReplicas = TavusReplica::count();
$activeReplicas = TavusReplica::where('status', 'ready')->count();
$totalVideos = TavusVideo::count();
$readyVideos = TavusVideo::where('status', 'ready')->count();
$activeConversations = TavusConversation::where('status', 'active')->count();
```

## Support Resources

- [Tavus Documentation](https://docs.tavus.io/)
- [Tavus API Reference](https://docs.tavus.io/api-reference/)
- [Tavus Platform](https://platform.tavus.io/)
- [Tavus Examples](https://github.com/Tavus-Engineering/tavus-examples)
