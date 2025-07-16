<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ChatbotService;
use App\Services\NLPService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;

class ChatbotController extends Controller
{
    private $chatbotService;
    private $nlpService;

    public function __construct(ChatbotService $chatbotService, NLPService $nlpService)
    {
        $this->chatbotService = $chatbotService;
        $this->nlpService = $nlpService;
    }

    public function chat(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'session_id' => 'nullable|string|max:100',
            'context' => 'nullable|array'
        ]);

        $sessionId = $request->session_id ?: Str::uuid()->toString();
        $message = trim($request->message);
        $context = $request->context ?? [];

        try {
            // Rate limiting per session
            $this->checkRateLimit($sessionId);

            // Process message with advanced NLP
            $response = $this->chatbotService->processMessage($message, $sessionId, $context);

            return response()->json([
                'success' => true,
                'session_id' => $sessionId,
                'response' => $response['response'],
                'intent' => $response['intent'],
                'confidence' => $response['confidence'],
                'suggestions' => $response['suggestions'],
                'entities' => $response['entities'] ?? [],
                'timestamp' => now()->toISOString(),
                'message_id' => Str::uuid()->toString()
            ]);

        } catch (\Exception $e) {
            \Log::error('Chatbot Error: ' . $e->getMessage(), [
                'session_id' => $sessionId,
                'message' => $message,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Maaf, terjadi kesalahan sistem. Silakan coba lagi atau hubungi (021) 1234-5678.',
                'error_code' => 'CHATBOT_ERROR'
            ], 500);
        }
    }

    public function getHistory(Request $request): JsonResponse
    {
        $request->validate([
            'session_id' => 'required|string'
        ]);

        try {
            $conversations = \App\Models\ChatbotConversation::where('session_id', $request->session_id)
                ->orderBy('created_at', 'asc')
                ->select(['user_message', 'bot_response', 'intent', 'confidence', 'created_at'])
                ->limit(50)
                ->get();

            return response()->json([
                'success' => true,
                'conversations' => $conversations,
                'total' => $conversations->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat mengambil riwayat percakapan.'
            ], 500);
        }
    }

    public function getSuggestions(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'nullable|string|max:100'
        ]);

        $suggestions = $this->chatbotService->getSmartSuggestions($request->query);

        return response()->json([
            'success' => true,
            'suggestions' => $suggestions
        ]);
    }

    public function feedback(Request $request): JsonResponse
    {
        $request->validate([
            'message_id' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'feedback' => 'nullable|string|max:500'
        ]);

        // Store feedback for improving chatbot
        \DB::table('chatbot_feedback')->insert([
            'message_id' => $request->message_id,
            'rating' => $request->rating,
            'feedback' => $request->feedback,
            'ip_address' => $request->ip(),
            'created_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Terima kasih atas feedback Anda!'
        ]);
    }

    private function checkRateLimit(string $sessionId): void
    {
        $key = "chatbot_rate_limit:{$sessionId}";
        $limit = 30; // 30 messages per hour
        $window = 3600; // 1 hour

        $current = \Cache::get($key, 0);
        
        if ($current >= $limit) {
            throw new \Exception('Rate limit exceeded. Please try again later.');
        }

        \Cache::put($key, $current + 1, $window);
    }
}