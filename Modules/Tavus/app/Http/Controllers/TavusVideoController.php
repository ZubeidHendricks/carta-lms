<?php

namespace Modules\Tavus\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Tavus\Services\TavusService;
use Modules\Tavus\Models\TavusVideo;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TavusVideoController extends Controller
{
    public function __construct(
        protected TavusService $tavusService
    ) {}

    /**
     * List all videos for the authenticated user
     */
    public function index(Request $request): JsonResponse
    {
        $query = TavusVideo::where('user_id', Auth::id())
            ->orWhereNull('user_id');

        if ($request->has('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $videos = $query->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $videos,
        ]);
    }

    /**
     * Show a specific video
     */
    public function show(TavusVideo $video): JsonResponse
    {
        // Sync status from Tavus
        $video = $this->tavusService->syncVideoStatus($video);

        return response()->json([
            'success' => true,
            'data' => $video,
        ]);
    }

    /**
     * Generate a new video
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'video_name' => 'required|string|max:255',
            'replica_id' => 'required|string',
            'script' => 'nullable|string',
            'background_source_url' => 'nullable|url',
            'course_id' => 'nullable|exists:courses,id',
            'variables' => 'nullable|array',
        ]);

        try {
            $video = $this->tavusService->generateVideo(
                $validated,
                Auth::id(),
                $validated['course_id'] ?? null
            );

            return response()->json([
                'success' => true,
                'message' => 'Video generation started',
                'data' => $video,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate video: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Sync video status with Tavus API
     */
    public function sync(TavusVideo $video): JsonResponse
    {
        try {
            $video = $this->tavusService->syncVideoStatus($video);

            return response()->json([
                'success' => true,
                'message' => 'Video synced successfully',
                'data' => $video,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to sync video: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a video
     */
    public function destroy(TavusVideo $video): JsonResponse
    {
        try {
            $this->tavusService->deleteVideo($video);

            return response()->json([
                'success' => true,
                'message' => 'Video deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete video: ' . $e->getMessage(),
            ], 500);
        }
    }
}
