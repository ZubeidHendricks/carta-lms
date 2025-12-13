<?php

namespace Modules\Tavus\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Tavus\Models\TavusWebhook;
use Modules\Tavus\Models\TavusReplica;
use Modules\Tavus\Models\TavusVideo;
use Modules\Tavus\Models\TavusConversation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class TavusWebhookController extends Controller
{
    /**
     * Handle incoming webhooks from Tavus
     */
    public function handle(Request $request): JsonResponse
    {
        $payload = $request->all();
        
        Log::info('Tavus Webhook Received', ['payload' => $payload]);

        // Store webhook for processing
        $webhook = TavusWebhook::create([
            'event_type' => $payload['event_type'] ?? 'unknown',
            'resource_id' => $payload['data']['id'] ?? null,
            'resource_type' => $this->getResourceType($payload['event_type'] ?? ''),
            'payload' => $payload,
        ]);

        // Process webhook immediately
        $this->processWebhook($webhook);

        return response()->json(['success' => true]);
    }

    /**
     * Process webhook event
     */
    protected function processWebhook(TavusWebhook $webhook): void
    {
        try {
            $eventType = $webhook->event_type;
            $data = $webhook->payload['data'] ?? [];

            match ($eventType) {
                'replica.trained' => $this->handleReplicaTrained($data),
                'replica.failed' => $this->handleReplicaFailed($data),
                'video.generated' => $this->handleVideoGenerated($data),
                'video.failed' => $this->handleVideoFailed($data),
                'conversation.started' => $this->handleConversationStarted($data),
                'conversation.ended' => $this->handleConversationEnded($data),
                default => Log::info('Unhandled webhook event type', ['event_type' => $eventType]),
            };

            $webhook->markAsProcessed();
        } catch (\Exception $e) {
            Log::error('Failed to process webhook', [
                'webhook_id' => $webhook->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function handleReplicaTrained(array $data): void
    {
        $replica = TavusReplica::where('replica_id', $data['replica_id'])->first();
        
        if ($replica) {
            $replica->update([
                'status' => 'ready',
                'trained_at' => now(),
                'metadata' => array_merge($replica->metadata ?? [], $data),
            ]);
        }
    }

    protected function handleReplicaFailed(array $data): void
    {
        $replica = TavusReplica::where('replica_id', $data['replica_id'])->first();
        
        if ($replica) {
            $replica->update([
                'status' => 'failed',
                'metadata' => array_merge($replica->metadata ?? [], $data),
            ]);
        }
    }

    protected function handleVideoGenerated(array $data): void
    {
        $video = TavusVideo::where('video_id', $data['video_id'])->first();
        
        if ($video) {
            $video->update([
                'status' => 'ready',
                'download_url' => $data['download_url'] ?? null,
                'hosted_url' => $data['hosted_url'] ?? null,
                'duration' => $data['duration'] ?? null,
                'completed_at' => now(),
                'metadata' => array_merge($video->metadata ?? [], $data),
            ]);
        }
    }

    protected function handleVideoFailed(array $data): void
    {
        $video = TavusVideo::where('video_id', $data['video_id'])->first();
        
        if ($video) {
            $video->update([
                'status' => 'failed',
                'metadata' => array_merge($video->metadata ?? [], $data),
            ]);
        }
    }

    protected function handleConversationStarted(array $data): void
    {
        $conversation = TavusConversation::where('conversation_id', $data['conversation_id'])->first();
        
        if ($conversation) {
            $conversation->update([
                'status' => 'active',
                'started_at' => now(),
                'properties' => array_merge($conversation->properties ?? [], $data),
            ]);
        }
    }

    protected function handleConversationEnded(array $data): void
    {
        $conversation = TavusConversation::where('conversation_id', $data['conversation_id'])->first();
        
        if ($conversation) {
            $conversation->update([
                'status' => 'ended',
                'ended_at' => now(),
                'duration' => $data['duration'] ?? null,
                'properties' => array_merge($conversation->properties ?? [], $data),
            ]);
        }
    }

    protected function getResourceType(string $eventType): string
    {
        if (str_contains($eventType, 'replica')) {
            return 'replica';
        } elseif (str_contains($eventType, 'video')) {
            return 'video';
        } elseif (str_contains($eventType, 'conversation')) {
            return 'conversation';
        }
        
        return 'unknown';
    }
}
