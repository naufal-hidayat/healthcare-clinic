<?php

namespace App\Http\Controllers;

use App\Services\ChatbotService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChatbotController extends Controller
{
    private $chatbotService;

    public function __construct(ChatbotService $chatbotService)
    {
        $this->chatbotService = $chatbotService;
    }

    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500',
            'session_id' => 'nullable|string'
        ]);

        $sessionId = $request->session_id ?: Str::uuid()->toString();
        $message = $request->message;

        $response = $this->chatbotService->processMessage($message, $sessionId);

        return response()->json([
            'session_id' => $sessionId,
            'response' => $response['response'],
            'intent' => $response['intent'],
            'confidence' => $response['confidence'],
            'suggestions' => $response['suggestions'],
            'timestamp' => now()->toISOString()
        ]);
    }

    public function getHistory(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string'
        ]);

        $conversations = \App\Models\ChatbotConversation::where('session_id', $request->session_id)
            ->orderBy('created_at', 'asc')
            ->get(['user_message', 'bot_response', 'created_at']);

        return response()->json($conversations);
    }
}