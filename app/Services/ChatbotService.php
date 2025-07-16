<?php

namespace App\Services;

use App\Models\ChatbotConversation;
use App\Models\MedicalKnowledgeBase;
use App\Models\Service;
use App\Models\Doctor;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ChatbotService
{
    private $openaiApiKey;
    private $model;
    private $contextPrompt;

    public function __construct()
    {
        $this->openaiApiKey = config('chatbot.openai.api_key');
        $this->model = config('chatbot.openai.model');
        $this->contextPrompt = config('chatbot.context_prompt');
    }

    public function processMessage(string $message, string $sessionId): array
    {
        // Normalize and analyze the message
        $normalizedMessage = $this->normalizeMessage($message);
        $intent = $this->detectIntent($normalizedMessage);
        
        // Get conversation context
        $context = $this->getConversationContext($sessionId);
        
        // Generate response based on intent
        $response = $this->generateResponse($normalizedMessage, $intent, $context);
        
        // Store conversation
        $conversation = ChatbotConversation::create([
            'session_id' => $sessionId,
            'user_message' => $message,
            'bot_response' => $response['text'],
            'context' => $context,
            'intent' => $intent,
            'confidence' => $response['confidence'],
            'ip_address' => request()->ip(),
        ]);

        return [
            'response' => $response['text'],
            'intent' => $intent,
            'confidence' => $response['confidence'],
            'suggestions' => $this->getSuggestions($intent),
        ];
    }

    private function normalizeMessage(string $message): string
    {
        return strtolower(trim(preg_replace('/[^\w\s]/', '', $message)));
    }

    private function detectIntent(string $message): string
    {
        $intents = [
            'greeting' => ['halo', 'hai', 'hello', 'selamat', 'pagi', 'siang', 'sore', 'malam'],
            'appointment' => ['janji', 'appointment', 'reservasi', 'daftar', 'booking'],
            'services' => ['layanan', 'service', 'biaya', 'tarif', 'harga'],
            'symptoms' => ['sakit', 'nyeri', 'demam', 'batuk', 'pilek', 'pusing', 'mual'],
            'doctors' => ['dokter', 'doctor', 'spesialis', 'ahli'],
            'location' => ['alamat', 'lokasi', 'dimana', 'tempat'],
            'hours' => ['jam', 'buka', 'tutup', 'operasional'],
            'emergency' => ['darurat', 'emergency', 'urgent', 'segera'],
            'contact' => ['telepon', 'phone', 'hubungi', 'kontak'],
            'goodbye' => ['bye', 'selamat tinggal', 'terima kasih', 'thanks'],
        ];

        foreach ($intents as $intent => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($message, $keyword)) {
                    return $intent;
                }
            }
        }

        return 'general';
    }

    private function getConversationContext(string $sessionId): array
    {
        $recentConversations = ChatbotConversation::where('session_id', $sessionId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return [
            'previous_intents' => $recentConversations->pluck('intent')->toArray(),
            'conversation_count' => $recentConversations->count(),
            'last_topics' => $recentConversations->pluck('user_message')->toArray(),
        ];
    }

    private function generateResponse(string $message, string $intent, array $context): array
    {
        // Try to get response from knowledge base first
        $knowledgeResponse = $this->searchKnowledgeBase($message, $intent);
        
        if ($knowledgeResponse) {
            return [
                'text' => $knowledgeResponse,
                'confidence' => 0.9
            ];
        }

        // Handle specific intents
        switch ($intent) {
            case 'greeting':
                return $this->handleGreeting($context);
            
            case 'appointment':
                return $this->handleAppointment();
            
            case 'services':
                return $this->handleServices($message);
            
            case 'symptoms':
                return $this->handleSymptoms($message);
            
            case 'doctors':
                return $this->handleDoctors($message);
            
            case 'location':
                return $this->handleLocation();
            
            case 'hours':
                return $this->handleHours();
            
            case 'emergency':
                return $this->handleEmergency();
            
            case 'contact':
                return $this->handleContact();
            
            case 'goodbye':
                return $this->handleGoodbye();
            
            default:
                return $this->handleWithOpenAI($message, $context);
        }
    }

    private function searchKnowledgeBase(string $message, string $intent): ?string
    {
        $knowledge = MedicalKnowledgeBase::where('is_active', true)
            ->where(function ($query) use ($message) {
                $query->whereRaw('JSON_CONTAINS(keywords, ?)', [json_encode($message)])
                    ->orWhere('title', 'LIKE', "%{$message}%")
                    ->orWhere('description', 'LIKE', "%{$message}%");
            })
            ->first();

        if ($knowledge) {
            $response = $knowledge->description;
            
            if ($knowledge->requires_doctor) {
                $response .= "\n\n⚠️ Untuk kondisi ini, sangat disarankan untuk konsultasi langsung dengan dokter kami. Hubungi (021) 1234-5678 untuk membuat janji.";
            }
            
            if ($knowledge->recommendations) {
                $response .= "\n\n💡 Rekomendasi: " . $knowledge->recommendations;
            }
            
            return $response;
        }

        return null;
    }

    private function handleGreeting(array $context): array
    {
        $responses = [
            "Halo! Selamat datang di HealthCare Plus. Saya adalah asisten virtual yang siap membantu Anda dengan informasi seputar layanan kesehatan kami. Ada yang bisa saya bantu?",
            "Hai! Terima kasih telah menghubungi HealthCare Plus. Saya di sini untuk membantu menjawab pertanyaan Anda tentang layanan kesehatan kami.",
            "Selamat datang! Saya adalah asisten virtual HealthCare Plus. Bagaimana saya bisa membantu Anda hari ini?"
        ];

        return [
            'text' => $responses[array_rand($responses)],
            'confidence' => 0.95
        ];
    }

    private function handleAppointment(): array
    {
        $doctors = Doctor::where('is_active', true)->get(['name', 'specialization']);
        
        $response = "📅 Untuk membuat janji dengan dokter, Anda bisa:\n\n";
        $response .= "1. Hubungi (021) 1234-5678\n";
        $response .= "2. Datang langsung ke klinik\n";
        $response .= "3. Melalui website kami\n\n";
        $response .= "🩺 Dokter yang tersedia:\n";
        
        foreach ($doctors->take(5) as $doctor) {
            $response .= "• Dr. {$doctor->name} - {$doctor->specialization}\n";
        }
        
        $response .= "\n⏰ Jam praktik: Senin-Sabtu 08:00-20:00, Minggu 08:00-16:00";

        return [
            'text' => $response,
            'confidence' => 0.9
        ];
    }

    private function handleServices(string $message): array
    {
        $services = Service::where('is_active', true)->get();
        
        if (str_contains($message, 'harga') || str_contains($message, 'biaya') || str_contains($message, 'tarif')) {
            $response = "💰 Informasi Biaya Layanan:\n\n";
            foreach ($services->take(10) as $service) {
                $response .= "• {$service->name}: Rp " . number_format((float) $service->price, 0, ',', '.') . "\n";
            }
            $response .= "\n💳 Kami menerima BPJS Kesehatan dan berbagai asuransi swasta.";
        } else {
            $response = "🏥 Layanan HealthCare Plus:\n\n";
            $categories = $services->groupBy('category');
            foreach ($categories as $category => $categoryServices) {
                $response .= "📋 " . ucfirst($category) . ":\n";
                foreach ($categoryServices->take(3) as $service) {
                    $response .= "• {$service->name}\n";
                }
                $response .= "\n";
            }
        }

        return [
            'text' => $response,
            'confidence' => 0.9
        ];
    }

    private function handleSymptoms(string $message): array
    {
        $symptoms = [
            'demam' => 'Demam bisa menjadi tanda infeksi. Istirahat yang cukup, minum banyak air, dan kompres hangat. Jika demam >38.5°C atau berlangsung >3 hari, segera konsultasi dokter.',
            'batuk' => 'Batuk bisa karena iritasi atau infeksi. Minum air hangat, madu, dan hindari udara dingin. Jika batuk berdarah atau tidak membaik dalam 2 minggu, hubungi dokter.',
            'pilek' => 'Pilek umumnya karena virus. Istirahat, minum air hangat, dan gunakan pelembab udara. Jika disertai demam tinggi atau tidak membaik dalam 1 minggu, konsultasi dokter.',
            'pusing' => 'Pusing bisa karena dehidrasi, kelelahan, atau masalah lain. Istirahat, minum air, dan hindari gerakan mendadak. Jika sering terjadi atau parah, periksa ke dokter.',
            'mual' => 'Mual bisa karena makanan, stress, atau kondisi lain. Makan sedikit tapi sering, hindari makanan berlemak. Jika muntah terus atau dehidrasi, segera ke dokter.'
        ];

        foreach ($symptoms as $symptom => $advice) {
            if (str_contains($message, $symptom)) {
                $response = "🩺 Tentang {$symptom}:\n\n{$advice}\n\n";
                $response .= "⚠️ Informasi ini hanya sebagai panduan umum. Untuk diagnosis dan pengobatan yang tepat, silakan konsultasi dengan dokter kami.\n\n";
                $response .= "📞 Hubungi (021) 1234-5678 untuk membuat janji atau (021) 999-8888 untuk darurat 24 jam.";
                
                return [
                    'text' => $response,
                    'confidence' => 0.85
                ];
            }
        }

        $response = "🩺 Saya memahami Anda mengalami gejala tertentu. Untuk evaluasi yang tepat, saya sarankan konsultasi langsung dengan dokter kami.\n\n";
        $response .= "📋 Beberapa layanan yang tersedia:\n";
        $response .= "• Konsultasi Dokter Umum\n";
        $response .= "• Pemeriksaan Laboratorium\n";
        $response .= "• Medical Check-up\n\n";
        $response .= "📞 Hubungi (021) 1234-5678 untuk membuat janji.";

        return [
            'text' => $response,
            'confidence' => 0.7
        ];
    }

    private function handleDoctors(string $message): array
    {
        $doctors = Doctor::where('is_active', true)->get();
        
        $response = "👨‍⚕️ Tim Dokter HealthCare Plus:\n\n";
        
        foreach ($doctors as $doctor) {
            $response .= "🩺 Dr. {$doctor->name}\n";
            $response .= "   Spesialisasi: {$doctor->specialization}\n";
            $response .= "   Pengalaman: {$doctor->experience}\n\n";
        }
        
        $response .= "📅 Untuk membuat janji dengan dokter pilihan Anda, hubungi (021) 1234-5678";

        return [
            'text' => $response,
            'confidence' => 0.9
        ];
    }

    private function handleLocation(): array
    {
        $response = "📍 Lokasi HealthCare Plus:\n\n";
        $response .= "🏥 Jl. Kesehatan No. 123\n";
        $response .= "   Jakarta Selatan 12345\n\n";
        $response .= "🚗 Akses Transportasi:\n";
        $response .= "• Dekat dengan stasiun MRT/KRL\n";
        $response .= "• Tersedia parkir yang luas\n";
        $response .= "• Akses mudah dari jalan utama\n\n";
        $response .= "🗺️ Gunakan GPS dengan mencari 'HealthCare Plus' atau alamat di atas.";

        return [
            'text' => $response,
            'confidence' => 0.95
        ];
    }

    private function handleHours(): array
    {
        $response = "⏰ Jam Operasional HealthCare Plus:\n\n";
        $response .= "📅 Senin - Sabtu: 08:00 - 20:00\n";
        $response .= "📅 Minggu: 08:00 - 16:00\n\n";
        $response .= "🚨 Layanan Darurat: 24 Jam\n";
        $response .= "   Hubungi: (021) 999-8888\n\n";
        $response .= "💡 Tips: Untuk menghindari antrian, disarankan membuat janji terlebih dahulu.";

        return [
            'text' => $response,
            'confidence' => 0.95
        ];
    }

    private function handleEmergency(): array
    {
        $response = "🚨 LAYANAN DARURAT 24 JAM\n\n";
        $response .= "📞 Hubungi segera: (021) 999-8888\n\n";
        $response .= "🏥 Alamat: Jl. Kesehatan No. 123, Jakarta Selatan\n\n";
        $response .= "⚠️ Kondisi yang memerlukan bantuan darurat:\n";
        $response .= "• Kesulitan bernapas\n";
        $response .= "• Nyeri dada hebat\n";
        $response .= "• Kehilangan kesadaran\n";
        $response .= "• Perdarahan hebat\n";
        $response .= "• Keracunan\n\n";
        $response .= "🚑 Jika sangat urgent, segera ke UGD atau hubungi 119";

        return [
            'text' => $response,
            'confidence' => 0.98
        ];
    }

    private function handleContact(): array
    {
        $response = "📞 Informasi Kontak HealthCare Plus:\n\n";
        $response .= "🏥 Telepon Utama: (021) 1234-5678\n";
        $response .= "🚨 Darurat 24 Jam: (021) 999-8888\n";
        $response .= "📧 Email: info@healthcareplus.com\n";
        $response .= "🌐 Website: www.healthcareplus.com\n\n";
        $response .= "📍 Alamat: Jl. Kesehatan No. 123, Jakarta Selatan 12345\n\n";
        $response .= "💬 Atau gunakan WhatsApp: 0812-3456-7890";

        return [
            'text' => $response,
            'confidence' => 0.95
        ];
    }

    private function handleGoodbye(): array
    {
        $responses = [
            "Terima kasih telah menggunakan layanan HealthCare Plus! Semoga sehat selalu. 🙏",
            "Senang bisa membantu Anda. Jaga kesehatan dan jangan ragu untuk menghubungi kami lagi! 😊",
            "Sampai jumpa! Ingat, kesehatan adalah investasi terbaik. Semoga hari Anda menyenangkan! 💚"
        ];

        return [
            'text' => $responses[array_rand($responses)],
            'confidence' => 0.9
        ];
    }

    private function handleWithOpenAI(string $message, array $context): array
    {
        if (!$this->openaiApiKey) {
            return [
                'text' => config('chatbot.fallback_responses.default'),
                'confidence' => 0.3
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->openaiApiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $this->contextPrompt
                    ],
                    [
                        'role' => 'user',
                        'content' => $message
                    ]
                ],
                'max_tokens' => config('chatbot.openai.max_tokens'),
                'temperature' => config('chatbot.openai.temperature'),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'text' => $data['choices'][0]['message']['content'],
                    'confidence' => 0.8
                ];
            }
        } catch (\Exception $e) {
            \Log::error('OpenAI API Error: ' . $e->getMessage());
        }

        return [
            'text' => config('chatbot.fallback_responses.default'),
            'confidence' => 0.3
        ];
    }

    private function getSuggestions(string $intent): array
    {
        $suggestions = [
            'greeting' => [
                'Jam operasional klinik',
                'Layanan yang tersedia',
                'Cara membuat janji',
                'Lokasi klinik'
            ],
            'appointment' => [
                'Dokter spesialis apa saja?',
                'Biaya konsultasi',
                'Jam praktik dokter',
                'Persiapan sebelum konsultasi'
            ],
            'services' => [
                'Medical check-up',
                'Laboratorium',
                'Konsultasi online',
                'Layanan gigi'
            ],
            'symptoms' => [
                'Kapan harus ke dokter?',
                'Pertolongan pertama',
                'Dokter spesialis mana?',
                'Buat janji sekarang'
            ],
            'general' => [
                'Informasi layanan',
                'Buat janji dokter',
                'Jam operasional',
                'Kontak darurat'
            ]
        ];

        return $suggestions[$intent] ?? $suggestions['general'];
    }
}