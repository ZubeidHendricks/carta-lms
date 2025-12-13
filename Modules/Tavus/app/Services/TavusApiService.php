<?php

namespace Modules\Tavus\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class TavusApiService
{
    protected Client $client;
    protected string $apiKey;
    protected string $apiUrl;

    public function __construct()
    {
        $this->apiKey = config('tavus.api_key');
        $this->apiUrl = config('tavus.api_url');
        
        $this->client = new Client([
            'base_uri' => $this->apiUrl,
            'headers' => [
                'x-api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'timeout' => 30,
        ]);
    }

    /**
     * Create a new replica
     */
    public function createReplica(array $data): array
    {
        try {
            $response = $this->client->post('/v2/replicas', [
                'json' => $data,
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            Log::error('Tavus API: Failed to create replica', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);
            throw $e;
        }
    }

    /**
     * Get replica by ID
     */
    public function getReplica(string $replicaId): array
    {
        try {
            $response = $this->client->get("/v2/replicas/{$replicaId}");
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            Log::error('Tavus API: Failed to get replica', [
                'replica_id' => $replicaId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * List all replicas
     */
    public function listReplicas(): array
    {
        try {
            $response = $this->client->get('/v2/replicas');
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            Log::error('Tavus API: Failed to list replicas', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Delete a replica
     */
    public function deleteReplica(string $replicaId): bool
    {
        try {
            $this->client->delete("/v2/replicas/{$replicaId}");
            return true;
        } catch (GuzzleException $e) {
            Log::error('Tavus API: Failed to delete replica', [
                'replica_id' => $replicaId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Generate a video
     */
    public function generateVideo(array $data): array
    {
        try {
            $response = $this->client->post('/v2/videos', [
                'json' => $data,
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            Log::error('Tavus API: Failed to generate video', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);
            throw $e;
        }
    }

    /**
     * Get video by ID
     */
    public function getVideo(string $videoId): array
    {
        try {
            $response = $this->client->get("/v2/videos/{$videoId}");
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            Log::error('Tavus API: Failed to get video', [
                'video_id' => $videoId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * List all videos
     */
    public function listVideos(): array
    {
        try {
            $response = $this->client->get('/v2/videos');
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            Log::error('Tavus API: Failed to list videos', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Delete a video
     */
    public function deleteVideo(string $videoId): bool
    {
        try {
            $this->client->delete("/v2/videos/{$videoId}");
            return true;
        } catch (GuzzleException $e) {
            Log::error('Tavus API: Failed to delete video', [
                'video_id' => $videoId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Create a conversation
     */
    public function createConversation(array $data): array
    {
        try {
            $response = $this->client->post('/v2/conversations', [
                'json' => $data,
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            Log::error('Tavus API: Failed to create conversation', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);
            throw $e;
        }
    }

    /**
     * Get conversation by ID
     */
    public function getConversation(string $conversationId): array
    {
        try {
            $response = $this->client->get("/v2/conversations/{$conversationId}");
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            Log::error('Tavus API: Failed to get conversation', [
                'conversation_id' => $conversationId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * End a conversation
     */
    public function endConversation(string $conversationId): bool
    {
        try {
            $this->client->delete("/v2/conversations/{$conversationId}");
            return true;
        } catch (GuzzleException $e) {
            Log::error('Tavus API: Failed to end conversation', [
                'conversation_id' => $conversationId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Create a persona
     */
    public function createPersona(array $data): array
    {
        try {
            $response = $this->client->post('/v2/personas', [
                'json' => $data,
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            Log::error('Tavus API: Failed to create persona', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);
            throw $e;
        }
    }

    /**
     * Get persona by ID
     */
    public function getPersona(string $personaId): array
    {
        try {
            $response = $this->client->get("/v2/personas/{$personaId}");
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            Log::error('Tavus API: Failed to get persona', [
                'persona_id' => $personaId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * List all personas
     */
    public function listPersonas(): array
    {
        try {
            $response = $this->client->get('/v2/personas');
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            Log::error('Tavus API: Failed to list personas', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Delete a persona
     */
    public function deletePersona(string $personaId): bool
    {
        try {
            $this->client->delete("/v2/personas/{$personaId}");
            return true;
        } catch (GuzzleException $e) {
            Log::error('Tavus API: Failed to delete persona', [
                'persona_id' => $personaId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
