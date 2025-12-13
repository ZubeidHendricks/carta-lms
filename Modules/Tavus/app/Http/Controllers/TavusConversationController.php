<?php

namespace Modules\Tavus\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Tavus\Services\TavusService;
use Modules\Tavus\Models\TavusConversation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TavusConversationController extends Controller
{
    public function __construct(
        protected TavusService $tavusService
    ) {}

    /**
     * List all conversations for the authenticated user
     */
    public function index(): JsonResponse
    {
        $conversations = TavusConversation::where('user_id', Auth::id())
            ->orWhereNull('user_id')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $conversations,
        ]);
    }

    /**
     * Show a specific conversation
     */
    public function show(TavusConversation $conversation): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $conversation,
        ]);
    }

    /**
     * Create a new conversation
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'conversation_name' => 'nullable|string|max:255',
            'replica_id' => 'required|string',
            'persona_id' => 'nullable|string',
            'conversational_context' => 'nullable|string',
            'custom_greeting' => 'nullable|string',
        ]);

        try {
            $conversation = $this->tavusService->createConversation($validated, Auth::id());

            return response()->json([
                'success' => true,
                'message' => 'Conversation created successfully',
                'data' => $conversation,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create conversation: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * End a conversation
     */
    public function end(TavusConversation $conversation): JsonResponse
    {
        try {
            $conversation = $this->tavusService->endConversation($conversation);

            return response()->json([
                'success' => true,
                'message' => 'Conversation ended successfully',
                'data' => $conversation,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to end conversation: ' . $e->getMessage(),
            ], 500);
        }
    }
}
