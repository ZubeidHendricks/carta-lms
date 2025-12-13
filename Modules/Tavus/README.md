# Tavus AI Video Integration Module

This module integrates Tavus AI video generation and conversational AI capabilities into the Mentor LMS platform.

## Features

### 1. **Digital Replica Management**
- Create AI-powered digital replicas of instructors
- Train replicas using training videos
- Manage multiple replicas per user
- Track replica training status

### 2. **AI Video Generation**
- Generate personalized videos using replicas
- Support for script-based video creation
- Custom background integration
- Variable replacement for personalization
- Course-specific video generation
- Automatic video download and storage

### 3. **Conversational AI**
- Create interactive conversations with replicas
- Custom greeting messages
- Contextual conversation management
- Real-time conversation tracking
- Session duration monitoring

### 4. **Webhook Integration**
- Automatic status updates via webhooks
- Event tracking for replicas, videos, and conversations
- Robust error handling and logging

## Installation

### 1. Environment Configuration

Add the following variables to your `.env` file:

```env
# Tavus API Configuration
TAVUS_API_KEY=your_api_key_here
TAVUS_API_URL=https://tavusapi.com
TAVUS_DEFAULT_REPLICA_ID=

# Video Settings
TAVUS_DEFAULT_VIDEO_NAME="LMS Video"
TAVUS_DEFAULT_BACKGROUND_URL=

# Conversation Settings
TAVUS_ENABLE_CONVERSATION=true
TAVUS_CONVERSATION_TIMEOUT=300

# Storage Settings
TAVUS_STORE_VIDEOS_LOCALLY=true
TAVUS_VIDEO_STORAGE_DISK=public
TAVUS_VIDEO_STORAGE_PATH=tavus-videos
```

### 2. Run Migrations

```bash
php artisan migrate
```

### 3. Publish Configuration (Optional)

```bash
php artisan vendor:publish --provider="Modules\Tavus\Providers\TavusServiceProvider" --tag="config"
```

## API Endpoints

### Replicas

#### List Replicas
```
GET /api/tavus/replicas
```

#### Create Replica
```
POST /api/tavus/replicas
Content-Type: application/json

{
    "replica_name": "John Instructor",
    "train_video_url": "https://example.com/training-video.mp4",
    "callback_url": "https://yourapp.com/tavus/webhook"
}
```

#### Get Replica
```
GET /api/tavus/replicas/{replica}
```

#### Sync Replica Status
```
POST /api/tavus/replicas/{replica}/sync
```

#### Delete Replica
```
DELETE /api/tavus/replicas/{replica}
```

### Videos

#### List Videos
```
GET /api/tavus/videos
GET /api/tavus/videos?course_id=1
GET /api/tavus/videos?status=ready
```

#### Generate Video
```
POST /api/tavus/videos
Content-Type: application/json

{
    "video_name": "Welcome Video",
    "replica_id": "r1234567890",
    "script": "Hello {{student_name}}, welcome to {{course_name}}!",
    "background_source_url": "https://example.com/background.jpg",
    "course_id": 1,
    "variables": {
        "student_name": "John",
        "course_name": "Advanced JavaScript"
    }
}
```

#### Get Video
```
GET /api/tavus/videos/{video}
```

#### Sync Video Status
```
POST /api/tavus/videos/{video}/sync
```

#### Delete Video
```
DELETE /api/tavus/videos/{video}
```

### Conversations

#### List Conversations
```
GET /api/tavus/conversations
```

#### Create Conversation
```
POST /api/tavus/conversations
Content-Type: application/json

{
    "conversation_name": "Student Support Chat",
    "replica_id": "r1234567890",
    "persona_id": "p1234567890",
    "conversational_context": "You are a helpful instructor assistant.",
    "custom_greeting": "Hello! How can I help you today?"
}
```

#### Get Conversation
```
GET /api/tavus/conversations/{conversation}
```

#### End Conversation
```
POST /api/tavus/conversations/{conversation}/end
```

### Webhooks

#### Webhook Endpoint
```
POST /api/tavus/webhook
```

This endpoint receives events from Tavus:
- `replica.trained` - Replica training completed
- `replica.failed` - Replica training failed
- `video.generated` - Video generation completed
- `video.failed` - Video generation failed
- `conversation.started` - Conversation started
- `conversation.ended` - Conversation ended

## Usage Examples

### Creating an Instructor Replica

```php
use Modules\Tavus\Services\TavusService;

$tavusService = app(TavusService::class);

$replica = $tavusService->createReplica([
    'replica_name' => 'Dr. Smith',
    'train_video_url' => 'https://storage.example.com/training-videos/dr-smith.mp4',
    'callback_url' => route('tavus.webhook'),
], auth()->id());
```

### Generating a Personalized Welcome Video

```php
$video = $tavusService->generateVideo([
    'video_name' => 'Course Welcome',
    'replica_id' => 'r1234567890',
    'script' => 'Welcome {{student_name}} to {{course_name}}! I\'m excited to be your instructor.',
    'variables' => [
        'student_name' => $student->name,
        'course_name' => $course->title,
    ],
], auth()->id(), $course->id);
```

### Creating an Interactive Conversation

```php
$conversation = $tavusService->createConversation([
    'conversation_name' => 'Q&A Session',
    'replica_id' => 'r1234567890',
    'conversational_context' => 'You are a knowledgeable instructor helping students understand complex topics.',
    'custom_greeting' => 'Hello! I\'m here to answer your questions about the course.',
], auth()->id());
```

### Checking Video Status

```php
$video = TavusVideo::find($videoId);
$video = $tavusService->syncVideoStatus($video);

if ($video->isReady()) {
    $url = $video->getVideoUrl();
    // Display or embed video
}
```

## Database Schema

### tavus_replicas
- `id` - Primary key
- `user_id` - User who created the replica
- `replica_id` - Tavus replica identifier
- `replica_name` - Name of the replica
- `training_video_url` - URL of training video
- `status` - pending, training, ready, failed
- `callback_url` - Webhook URL
- `metadata` - Additional data from Tavus
- `trained_at` - When training completed

### tavus_videos
- `id` - Primary key
- `user_id` - User who created the video
- `course_id` - Associated course
- `video_id` - Tavus video identifier
- `video_name` - Name of the video
- `replica_id` - Replica used
- `script` - Video script
- `background_source_url` - Background image/video URL
- `status` - pending, processing, ready, failed
- `download_url` - Tavus download URL
- `hosted_url` - Tavus hosted URL
- `local_path` - Local storage path
- `duration` - Video duration in seconds
- `variables` - Script variables
- `metadata` - Additional data
- `completed_at` - When video was ready

### tavus_conversations
- `id` - Primary key
- `user_id` - User who created the conversation
- `conversation_id` - Tavus conversation identifier
- `conversation_name` - Name of the conversation
- `replica_id` - Replica used
- `persona_id` - Persona identifier
- `conversational_context` - Context/instructions
- `custom_greeting` - Custom greeting message
- `status` - active, ended, failed
- `duration` - Conversation duration
- `properties` - Additional properties
- `started_at` - When conversation started
- `ended_at` - When conversation ended

### tavus_webhooks
- `id` - Primary key
- `event_type` - Type of event
- `resource_id` - Related resource ID
- `resource_type` - replica, video, conversation
- `payload` - Full webhook payload
- `processed` - Whether webhook was processed
- `processed_at` - When webhook was processed

## Integration with LMS

### Course Introduction Videos
Generate personalized welcome videos for each student when they enroll in a course.

### Assignment Feedback
Create personalized video feedback for student assignments using replicas.

### Virtual Office Hours
Use conversational AI to provide 24/7 virtual office hours with instructor replicas.

### Course Announcements
Generate personalized announcement videos for course updates.

### Student Support
Interactive conversations for common student questions and support.

## Best Practices

1. **Replica Quality**: Use high-quality training videos (1080p minimum) with clear audio
2. **Script Optimization**: Keep scripts natural and conversational
3. **Variable Usage**: Use variables for personalization to maximize engagement
4. **Webhook Configuration**: Always configure webhooks for automatic status updates
5. **Storage Management**: Enable local storage for frequently accessed videos
6. **Error Handling**: Monitor logs for failed generations and retry as needed

## Troubleshooting

### Replica Training Fails
- Verify training video quality and format
- Check video duration (minimum 2 minutes recommended)
- Ensure clear audio and frontal face visibility

### Video Generation Timeout
- Check API key validity
- Verify replica is in 'ready' status
- Review script for unsupported characters

### Conversation Not Starting
- Confirm replica is trained and ready
- Verify persona ID if using custom personas
- Check conversation timeout settings

## Security Considerations

1. **API Key Protection**: Never commit API keys to version control
2. **Webhook Validation**: Implement signature verification for webhooks
3. **Access Control**: Use Laravel policies to control replica/video access
4. **Data Privacy**: Handle student data in compliance with regulations
5. **Rate Limiting**: Implement rate limiting on API endpoints

## Support

For Tavus API documentation: https://docs.tavus.io
For module issues: Contact your development team

## License

This module is part of the Mentor LMS system.
