<?php

namespace Modules\Tavus\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Tavus\Services\TavusService;
use Modules\Tavus\Models\TavusReplica;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TavusReplicaController extends Controller
{
    public function __construct(
        protected TavusService $tavusService
    ) {}

    /**
     * List all replicas for the authenticated user
     */
    public function index(): JsonResponse
    {
        $replicas = TavusReplica::where('user_id', Auth::id())
            ->orWhereNull('user_id')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $replicas,
        ]);
    }

    /**
     * Show a specific replica
     */
    public function show(TavusReplica $replica): JsonResponse
    {
        // Sync status from Tavus
        $replica = $this->tavusService->syncReplicaStatus($replica);

        return response()->json([
            'success' => true,
            'data' => $replica,
        ]);
    }

    /**
     * Create a new replica
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'replica_name' => 'required|string|max:255',
            'train_video_url' => 'required|url',
            'callback_url' => 'nullable|url',
        ]);

        try {
            $replica = $this->tavusService->createReplica($validated, Auth::id());

            return response()->json([
                'success' => true,
                'message' => 'Replica created successfully',
                'data' => $replica,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create replica: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Sync replica status with Tavus API
     */
    public function sync(TavusReplica $replica): JsonResponse
    {
        try {
            $replica = $this->tavusService->syncReplicaStatus($replica);

            return response()->json([
                'success' => true,
                'message' => 'Replica synced successfully',
                'data' => $replica,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to sync replica: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a replica
     */
    public function destroy(TavusReplica $replica): JsonResponse
    {
        try {
            $this->tavusService->deleteReplica($replica);

            return response()->json([
                'success' => true,
                'message' => 'Replica deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete replica: ' . $e->getMessage(),
            ], 500);
        }
    }
}
