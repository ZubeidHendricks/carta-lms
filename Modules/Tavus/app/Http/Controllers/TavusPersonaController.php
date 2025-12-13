<?php

namespace Modules\Tavus\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Tavus\Services\TavusService;
use Modules\Tavus\Services\TavusApiService;
use Modules\Tavus\Models\TavusPersona;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TavusPersonaController extends Controller
{
    public function __construct(
        protected TavusService $tavusService,
        protected TavusApiService $apiService
    ) {}

    /**
     * List all personas for the authenticated user
     */
    public function index(Request $request): JsonResponse
    {
        $query = TavusPersona::query();

        // Filter by user if not admin
        if (!$request->user()->isAdmin()) {
            $query->where(function ($q) use ($request) {
                $q->where('user_id', $request->user()->id)
                  ->orWhereNull('user_id'); // Include public personas
            });
        }

        // Filter by active status
        if ($request->has('active')) {
            $query->where('is_active', $request->boolean('active'));
        }

        $personas = $query->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $personas,
        ]);
    }

    /**
     * Show a specific persona
     */
    public function show(TavusPersona $persona): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $persona,
        ]);
    }

    /**
     * Create a new persona
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'persona_name' => 'required|string|max:255',
            'system_prompt' => 'nullable|string',
            'context' => 'nullable|string',
            'layers' => 'nullable|array',
            'default_replica_id' => 'nullable|string',
            'properties' => 'nullable|array',
        ]);

        try {
            // Create persona in Tavus
            $response = $this->apiService->createPersona([
                'persona_name' => $validated['persona_name'],
                'system_prompt' => $validated['system_prompt'] ?? null,
                'context' => $validated['context'] ?? null,
                'layers' => $validated['layers'] ?? null,
                'properties' => $validated['properties'] ?? null,
            ]);

            // Store in database
            $persona = TavusPersona::create([
                'user_id' => Auth::id(),
                'persona_id' => $response['persona_id'],
                'persona_name' => $validated['persona_name'],
                'system_prompt' => $validated['system_prompt'] ?? null,
                'context' => $validated['context'] ?? null,
                'layers' => $validated['layers'] ?? null,
                'default_replica_id' => $validated['default_replica_id'] ?? null,
                'properties' => $validated['properties'] ?? null,
                'metadata' => $response,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Persona created successfully',
                'data' => $persona,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create persona: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update a persona
     */
    public function update(Request $request, TavusPersona $persona): JsonResponse
    {
        $validated = $request->validate([
            'persona_name' => 'sometimes|string|max:255',
            'system_prompt' => 'nullable|string',
            'context' => 'nullable|string',
            'layers' => 'nullable|array',
            'default_replica_id' => 'nullable|string',
            'properties' => 'nullable|array',
            'is_active' => 'sometimes|boolean',
        ]);

        try {
            $persona->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Persona updated successfully',
                'data' => $persona->fresh(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update persona: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a persona
     */
    public function destroy(TavusPersona $persona): JsonResponse
    {
        try {
            // Delete from Tavus
            $this->apiService->deletePersona($persona->persona_id);
            
            // Delete from database
            $persona->delete();

            return response()->json([
                'success' => true,
                'message' => 'Persona deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete persona: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Toggle persona active status
     */
    public function toggle(TavusPersona $persona): JsonResponse
    {
        $persona->update([
            'is_active' => !$persona->is_active,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Persona status updated',
            'data' => $persona->fresh(),
        ]);
    }

    /**
     * Create a preset persona
     */
    public function createPreset(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:instructor,tutor,mentor,support',
        ]);

        try {
            $persona = TavusPersona::createPreset(
                $validated['type'],
                Auth::id()
            );

            return response()->json([
                'success' => true,
                'message' => 'Preset persona created successfully',
                'data' => $persona,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create preset: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get available preset types
     */
    public function presets(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                [
                    'type' => 'instructor',
                    'name' => 'Course Instructor',
                    'description' => 'Professional educator focused on teaching and clarity',
                ],
                [
                    'type' => 'tutor',
                    'name' => 'Personal Tutor',
                    'description' => 'Friendly tutor providing personalized help',
                ],
                [
                    'type' => 'mentor',
                    'name' => 'Career Mentor',
                    'description' => 'Experienced mentor for career guidance',
                ],
                [
                    'type' => 'support',
                    'name' => 'Student Support',
                    'description' => 'Helpful assistant for student queries',
                ],
            ],
        ]);
    }

    /**
     * Get persona configuration for API usage
     */
    public function configuration(TavusPersona $persona): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $persona->getApiConfiguration(),
        ]);
    }
}
