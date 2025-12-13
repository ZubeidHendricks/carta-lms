<?php

namespace Modules\Tavus\Services;

use Modules\Tavus\Models\TavusReplica;
use Modules\Tavus\Models\TavusVideo;
use Modules\Tavus\Models\TavusConversation;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class TavusService
{
    public function __construct(
        protected TavusApiService $apiService
    ) {}

    /**
     * Create and store a new replica
     */
    public function createReplica(array $data, ?int $userId = null): TavusReplica
    {
        $response = $this->apiService->createReplica($data);
        
        return TavusReplica::create([
            'user_id' => $userId,
            'replica_id' => $response['replica_id'],
            'replica_name' => $data['replica_name'] ?? 'Untitled Replica',
            'training_video_url' => $data['train_video_url'] ?? null,
            'status' => $response['status'] ?? 'pending',
            'callback_url' => $data['callback_url'] ?? null,
            'metadata' => $response,
        ]);
    }

    /**
     * Sync replica status from Tavus API
     */
    public function syncReplicaStatus(TavusReplica $replica): TavusReplica
    {
        try {
            $response = $this->apiService->getReplica($replica->replica_id);
            
            $replica->update([
                'status' => $response['status'] ?? $replica->status,
                'metadata' => $response,
                'trained_at' => ($response['status'] === 'ready') ? now() : $replica->trained_at,
            ]);
            
            return $replica->fresh();
        } catch (\Exception $e) {
            Log::error('Failed to sync replica status', [
                'replica_id' => $replica->replica_id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Generate a video
     */
    public function generateVideo(array $data, ?int $userId = null, ?int $courseId = null): TavusVideo
    {
        $response = $this->apiService->generateVideo($data);
        
        return TavusVideo::create([
            'user_id' => $userId,
            'course_id' => $courseId,
            'video_id' => $response['video_id'],
            'video_name' => $data['video_name'] ?? 'Untitled Video',
            'replica_id' => $data['replica_id'],
            'script' => $data['script'] ?? null,
            'background_source_url' => $data['background_source_url'] ?? null,
            'status' => $response['status'] ?? 'pending',
            'variables' => $data['variables'] ?? null,
            'metadata' => $response,
        ]);
    }

    /**
     * Sync video status from Tavus API
     */
    public function syncVideoStatus(TavusVideo $video): TavusVideo
    {
        try {
            $response = $this->apiService->getVideo($video->video_id);
            
            $updateData = [
                'status' => $response['status'] ?? $video->status,
                'download_url' => $response['download_url'] ?? $video->download_url,
                'hosted_url' => $response['hosted_url'] ?? $video->hosted_url,
                'duration' => $response['duration'] ?? $video->duration,
                'metadata' => $response,
            ];

            if ($response['status'] === 'ready' && !$video->completed_at) {
                $updateData['completed_at'] = now();
                
                // Download video locally if configured
                if (config('tavus.store_videos_locally') && isset($response['download_url'])) {
                    $this->downloadVideo($video, $response['download_url']);
                }
            }
            
            $video->update($updateData);
            
            return $video->fresh();
        } catch (\Exception $e) {
            Log::error('Failed to sync video status', [
                'video_id' => $video->video_id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Download video to local storage
     */
    protected function downloadVideo(TavusVideo $video, string $url): void
    {
        try {
            $disk = config('tavus.video_storage_disk');
            $path = config('tavus.video_storage_path');
            $filename = "{$video->video_id}.mp4";
            $fullPath = "{$path}/{$filename}";

            $contents = file_get_contents($url);
            Storage::disk($disk)->put($fullPath, $contents);

            $video->update(['local_path' => $fullPath]);
            
            Log::info('Video downloaded successfully', [
                'video_id' => $video->video_id,
                'path' => $fullPath,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to download video', [
                'video_id' => $video->video_id,
                'url' => $url,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Create a conversation
     */
    public function createConversation(array $data, ?int $userId = null): TavusConversation
    {
        $response = $this->apiService->createConversation($data);
        
        return TavusConversation::create([
            'user_id' => $userId,
            'conversation_id' => $response['conversation_id'],
            'conversation_name' => $data['conversation_name'] ?? null,
            'replica_id' => $data['replica_id'],
            'persona_id' => $data['persona_id'] ?? null,
            'conversational_context' => $data['conversational_context'] ?? null,
            'custom_greeting' => $data['custom_greeting'] ?? null,
            'status' => 'active',
            'properties' => $response['properties'] ?? null,
            'started_at' => now(),
        ]);
    }

    /**
     * End a conversation
     */
    public function endConversation(TavusConversation $conversation): TavusConversation
    {
        try {
            $this->apiService->endConversation($conversation->conversation_id);
            
            $conversation->update([
                'status' => 'ended',
                'ended_at' => now(),
                'duration' => $conversation->getDuration(),
            ]);
            
            return $conversation->fresh();
        } catch (\Exception $e) {
            Log::error('Failed to end conversation', [
                'conversation_id' => $conversation->conversation_id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Delete a replica
     */
    public function deleteReplica(TavusReplica $replica): bool
    {
        try {
            $this->apiService->deleteReplica($replica->replica_id);
            $replica->delete();
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to delete replica', [
                'replica_id' => $replica->replica_id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Delete a video
     */
    public function deleteVideo(TavusVideo $video): bool
    {
        try {
            $this->apiService->deleteVideo($video->video_id);
            
            // Delete local file if exists
            if ($video->local_path) {
                Storage::disk(config('tavus.video_storage_disk'))->delete($video->local_path);
            }
            
            $video->delete();
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to delete video', [
                'video_id' => $video->video_id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
