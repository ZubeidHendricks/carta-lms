<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\Gamification\GamificationService;
use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Support\Facades\Auth;

class GamificationController extends Controller
{
    public function __construct(private GamificationService $gamificationService)
    {
    }

    /**
     * Get user gamification stats
     */
    public function getUserStats(): JsonResponse
    {
        $user = Auth::user();
        $stats = $this->gamificationService->getUserStats($user);

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Get user's earned badges
     */
    public function getUserBadges(): JsonResponse
    {
        $user = Auth::user();
        $badges = $user->learnerBadges()->with('badge')->get();

        return response()->json([
            'success' => true,
            'data' => $badges,
        ]);
    }

    /**
     * Get user's points history
     */
    public function getPointsHistory(Request $request): JsonResponse
    {
        $user = Auth::user();
        $perPage = $request->input('per_page', 20);
        
        $points = $user->learnerPoints()
            ->orderByDesc('created_at')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $points,
        ]);
    }

    /**
     * Get leaderboard
     */
    public function getLeaderboard(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 10);
        $leaderboard = $this->gamificationService->getLeaderboard($limit);

        return response()->json([
            'success' => true,
            'data' => $leaderboard,
        ]);
    }

    /**
     * Update course progress
     */
    public function updateProgress(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'lessons_completed' => 'required|integer|min:0',
            'total_lessons' => 'required|integer|min:1',
            'time_spent' => 'nullable|integer|min:0',
        ]);

        $user = Auth::user();
        $progress = $this->gamificationService->trackProgress(
            $user,
            $validated['course_id'],
            $validated['lessons_completed'],
            $validated['total_lessons'],
            $validated['time_spent'] ?? 0
        );

        return response()->json([
            'success' => true,
            'data' => $progress,
            'message' => 'Progress updated successfully',
        ]);
    }

    /**
     * Get user's course progress
     */
    public function getCourseProgress(int $courseId): JsonResponse
    {
        $user = Auth::user();
        $progress = $user->learnerProgress()
            ->where('course_id', $courseId)
            ->first();

        return response()->json([
            'success' => true,
            'data' => $progress,
        ]);
    }

    /**
     * Get all user's progress
     */
    public function getAllProgress(): JsonResponse
    {
        $user = Auth::user();
        $progress = $user->learnerProgress()
            ->with('course')
            ->orderByDesc('last_accessed_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $progress,
        ]);
    }
}
