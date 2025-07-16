<?php

namespace App\Services;

class NLPService
{
    private $stopWords;
    private $medicalTerms;
    private $symptoms;
    private $intentPatterns;

    public function __construct()
    {
        $this->initializeData();
    }

    private function initializeData(): void
    {
        $this->stopWords = [
            'adalah', 'dan', 'atau', 'yang', 'di', 'ke', 'dari', 'untuk', 'pada', 'dengan',
            'ini', 'itu', 'saya', 'anda', 'kamu', 'mereka', 'kami', 'kita', 'nya', 'mu',
            'ku', 'ada', 'tidak', 'bisa', 'dapat', 'akan', 'sudah', 'belum', 'jika',
            'kalau', 'ketika', 'karena', 'sebab', 'maka', 'jadi', 'lalu', 'kemudian'
        ];

        $this->medicalTerms = [
            'demam' => ['fever', 'panas', 'meriang'],
            'batuk' => ['cough', 'batuk kering', 'batuk berdahak'],
            'pilek' => ['runny nose', 'hidung meler', 'flu'],
            'sakit kepala' => ['headache', 'pusing', 'migrain'],
            'mual' => ['nausea', 'eneg', 'muntah'],
            'diare' => ['diarrhea', 'mencret', 'perut mules'],
            'nyeri' => ['pain', 'sakit', 'perih'],
            'sesak napas' => ['shortness of breath', 'susah napas', 'sesak dada'],
            'jantung berdebar' => ['palpitation', 'detak jantung cepat'],
            'vertigo' => ['dizzy', 'pusing berputar', 'kepala ringan']
        ];

        $this->symptoms = [
            'ringan' => ['batuk', 'pilek', 'sakit kepala ringan', 'kelelahan'],
            'sedang' => ['demam', 'mual', 'nyeri otot', 'diare'],
            'berat' => ['sesak napas', 'nyeri dada', 'demam tinggi', 'muntah darah'],
            'darurat' => ['tidak sadarkan diri', 'kejang', 'perdarahan hebat', 'sesak napas berat']
        ];

        $this->intentPatterns = [
            'greeting' => [
                'pattern' => '/^(hai|halo|hello|selamat|hei|hi)\b/i',
                'confidence' => 0.9
            ],
            'appointment' => [
                'pattern' => '/(janji|daftar|booking|reservasi|appointment|konsultasi)/i',
                'confidence' => 0.8
            ],
            'symptoms' => [
                'pattern' => '/(sakit|nyeri|demam|batuk|pilek|pusing|mual|muntah|diare|sesak|palpitasi)/i',
                'confidence' => 0.85
            ],
            'emergency' => [
                'pattern' => '/(darurat|emergency|urgent|segera|bantuan|tolong|gawat)/i',
                'confidence' => 0.95
            ],
            'services' => [
                'pattern' => '/(layanan|service|biaya|harga|tarif|fasilitas)/i',
                'confidence' => 0.8
            ],
            'doctors' => [
                'pattern' => '/(dokter|doctor|spesialis|ahli|dr\.)/i',
                'confidence' => 0.8
            ],
            'location' => [
                'pattern' => '/(alamat|lokasi|dimana|tempat|maps|peta)/i',
                'confidence' => 0.9
            ],
            'hours' => [
                'pattern' => '/(jam|buka|tutup|operasional|waktu|schedule)/i',
                'confidence' => 0.9
            ],
            'contact' => [
                'pattern' => '/(telepon|phone|hubungi|kontak|email|whatsapp)/i',
                'confidence' => 0.9
            ]
        ];
    }

    public function analyzeMessage(string $message): array
    {
        $normalizedMessage = $this->normalizeText($message);
        
        return [
            'intent' => $this->detectIntent($normalizedMessage),
            'entities' => $this->extractEntities($normalizedMessage),
            'sentiment' => $this->analyzeSentiment($normalizedMessage),
            'urgency' => $this->assessUrgency($normalizedMessage),
            'medical_terms' => $this->extractMedicalTerms($normalizedMessage),
            'keywords' => $this->extractKeywords($normalizedMessage)
        ];
    }

    private function normalizeText(string $text): string
    {
        // Convert to lowercase
        $text = mb_strtolower($text, 'UTF-8');
        
        // Remove extra whitespace
        $text = preg_replace('/\s+/', ' ', $text);
        
        // Remove punctuation except important medical indicators
        $text = preg_replace('/[^\w\s\-\/\.]/', ' ', $text);
        
        return trim($text);
    }

    public function detectIntent(string $message): array
    {
        $bestIntent = 'general';
        $bestConfidence = 0.0;
        $matches = [];

        foreach ($this->intentPatterns as $intent => $pattern) {
            if (preg_match($pattern['pattern'], $message, $patternMatches)) {
                $confidence = $pattern['confidence'];
                
                // Boost confidence based on context
                $confidence = $this->adjustConfidenceByContext($intent, $message, $confidence);
                
                if ($confidence > $bestConfidence) {
                    $bestIntent = $intent;
                    $bestConfidence = $confidence;
                    $matches = $patternMatches;
                }
            }
        }

        return [
            'intent' => $bestIntent,
            'confidence' => $bestConfidence,
            'matches' => $matches
        ];
    }

    private function adjustConfidenceByContext(string $intent, string $message, float $baseConfidence): float
    {
        $confidence = $baseConfidence;

        // Boost confidence for medical terms in symptoms intent
        if ($intent === 'symptoms') {
            foreach ($this->medicalTerms as $term => $synonyms) {
                if (strpos($message, $term) !== false) {
                    $confidence += 0.1;
                }
                foreach ($synonyms as $synonym) {
                    if (strpos($message, $synonym) !== false) {
                        $confidence += 0.05;
                    }
                }
            }
        }

        // Boost confidence for urgent keywords in emergency intent
        if ($intent === 'emergency') {
            $urgentKeywords = ['segera', 'cepat', 'langsung', 'sekarang', 'tolong'];
            foreach ($urgentKeywords as $keyword) {
                if (strpos($message, $keyword) !== false) {
                    $confidence += 0.05;
                }
            }
        }

        return min($confidence, 1.0);
    }

    public function extractEntities(string $message): array
    {
        $entities = [];

        // Extract time entities
        if (preg_match('/(\d{1,2}):(\d{2})/', $message, $matches)) {
            $entities['time'] = $matches[0];
        }

        // Extract date entities
        if (preg_match('/(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{2,4})/', $message, $matches)) {
            $entities['date'] = $matches[0];
        }

        // Extract doctor specializations
        $specializations = ['umum', 'anak', 'gigi', 'mata', 'jantung', 'kulit', 'tht', 'neurologi'];
        foreach ($specializations as $spec) {
            if (strpos($message, $spec) !== false) {
                $entities['specialization'] = $spec;
                break;
            }
        }

        // Extract phone numbers
        if (preg_match('/(\+62|08)\d{8,12}/', $message, $matches)) {
            $entities['phone'] = $matches[0];
        }

        return $entities;
    }

    public function analyzeSentiment(string $message): array
    {
        $positiveWords = ['baik', 'bagus', 'senang', 'puas', 'terima kasih', 'sukses', 'hebat', 'mantap'];
        $negativeWords = ['buruk', 'jelek', 'kecewa', 'marah', 'sedih', 'sakit', 'susah', 'sulit', 'parah'];
        $neutralWords = ['biasa', 'standar', 'cukup', 'lumayan'];

        $positiveCount = 0;
        $negativeCount = 0;
        $neutralCount = 0;

        foreach ($positiveWords as $word) {
            if (strpos($message, $word) !== false) {
                $positiveCount++;
            }
        }

        foreach ($negativeWords as $word) {
            if (strpos($message, $word) !== false) {
                $negativeCount++;
            }
        }

        foreach ($neutralWords as $word) {
            if (strpos($message, $word) !== false) {
                $neutralCount++;
            }
        }

        if ($positiveCount > $negativeCount && $positiveCount > $neutralCount) {
            $sentiment = 'positive';
            $score = min($positiveCount / max(1, $negativeCount + $neutralCount), 1.0);
        } elseif ($negativeCount > $positiveCount && $negativeCount > $neutralCount) {
            $sentiment = 'negative';
            $score = min($negativeCount / max(1, $positiveCount + $neutralCount), 1.0);
        } else {
            $sentiment = 'neutral';
            $score = 0.5;
        }

        return [
            'sentiment' => $sentiment,
            'score' => $score,
            'positive_count' => $positiveCount,
            'negative_count' => $negativeCount,
            'neutral_count' => $neutralCount
        ];
    }

    public function assessUrgency(string $message): array
    {
        $urgencyLevels = [
            'critical' => ['tidak sadar', 'kejang', 'perdarahan hebat', 'sesak napas berat', 'nyeri dada hebat'],
            'high' => ['demam tinggi', 'muntah darah', 'pingsan', 'sesak napas', 'nyeri dada'],
            'medium' => ['demam', 'mual muntah', 'diare berat', 'sakit kepala hebat'],
            'low' => ['batuk', 'pilek', 'sakit kepala ringan', 'kelelahan']
        ];

        foreach ($urgencyLevels as $level => $keywords) {
            foreach ($keywords as $keyword) {
                if (strpos($message, $keyword) !== false) {
                    $confidence = $this->calculateUrgencyConfidence($level, $message);
                    return [
                        'level' => $level,
                        'confidence' => $confidence,
                        'keyword' => $keyword
                    ];
                }
            }
        }

        return [
            'level' => 'unknown',
            'confidence' => 0.0,
            'keyword' => null
        ];
    }

    private function calculateUrgencyConfidence(string $level, string $message): float
    {
        $baseConfidence = [
            'critical' => 0.95,
            'high' => 0.85,
            'medium' => 0.7,
            'low' => 0.6,
            'unknown' => 0.3
        ];

        $confidence = $baseConfidence[$level] ?? 0.3;

        // Boost confidence for urgent indicators
        $urgentIndicators = ['segera', 'darurat', 'cepat', 'tolong', 'bantuan'];
        foreach ($urgentIndicators as $indicator) {
            if (strpos($message, $indicator) !== false) {
                $confidence = min($confidence + 0.1, 1.0);
            }
        }

        return $confidence;
    }

    public function extractMedicalTerms(string $message): array
    {
        $foundTerms = [];

        foreach ($this->medicalTerms as $term => $synonyms) {
            if (strpos($message, $term) !== false) {
                $foundTerms[] = [
                    'term' => $term,
                    'type' => 'primary',
                    'position' => strpos($message, $term)
                ];
            }

            foreach ($synonyms as $synonym) {
                if (strpos($message, $synonym) !== false) {
                    $foundTerms[] = [
                        'term' => $synonym,
                        'type' => 'synonym',
                        'primary_term' => $term,
                        'position' => strpos($message, $synonym)
                    ];
                }
            }
        }

        // Sort by position in message
        usort($foundTerms, function($a, $b) {
            return $a['position'] - $b['position'];
        });

        return $foundTerms;
    }

    public function extractKeywords(string $message): array
    {
        $words = explode(' ', $message);
        $keywords = [];

        foreach ($words as $word) {
            $word = trim($word);
            
            // Skip empty words and stop words
            if (empty($word) || in_array($word, $this->stopWords)) {
                continue;
            }

            // Skip very short words (less than 3 characters)
            if (strlen($word) < 3) {
                continue;
            }

            $keywords[] = $word;
        }

        // Remove duplicates and return
        return array_unique($keywords);
    }

    public function generateResponse(array $analysis, string $originalMessage): string
    {
        $intent = $analysis['intent']['intent'];
        $urgency = $analysis['urgency'];
        $medicalTerms = $analysis['medical_terms'];

        // Handle critical urgency first
        if ($urgency['level'] === 'critical') {
            return "🚨 DARURAT! Berdasarkan gejala yang Anda sebutkan, segera hubungi:\n\n📞 (021) 999-8888 (Darurat 24 Jam)\n🏥 Atau datang langsung ke UGD kami\n\n⚠️ Jangan menunda, kondisi ini memerlukan penanganan medis segera!";
        }

        // Handle high urgency
        if ($urgency['level'] === 'high') {
            return "⚠️ Kondisi yang Anda alami perlu perhatian medis segera. Saya sarankan:\n\n1. Hubungi (021) 1234-5678 untuk membuat janji darurat\n2. Atau hubungi (021) 999-8888 jika kondisi memburuk\n3. Datang langsung ke klinik jika memungkinkan\n\n💡 Sementara itu, pastikan Anda beristirahat dan jangan melakukan aktivitas berat.";
        }

        // Generate response based on intent and context
        return $this->generateContextualResponse($intent, $analysis, $originalMessage);
    }

    private function generateContextualResponse(string $intent, array $analysis, string $originalMessage): string
    {
        $medicalTerms = $analysis['medical_terms'];
        $sentiment = $analysis['sentiment'];

        switch ($intent) {
            case 'symptoms':
                if (!empty($medicalTerms)) {
                    $primaryTerm = $medicalTerms[0]['term'];
                    return $this->getSymptomAdvice($primaryTerm);
                }
                return "Saya memahami Anda mengalami gejala tertentu. Untuk evaluasi yang tepat, saya sarankan konsultasi dengan dokter kami. Hubungi (021) 1234-5678 untuk membuat janji.";

            case 'greeting':
                if ($sentiment['sentiment'] === 'negative') {
                    return "Halo, saya melihat Anda mungkin sedang mengalami sesuatu yang mengganggu. Bagaimana saya bisa membantu Anda hari ini? Jangan ragu untuk bercerita tentang keluhan Anda.";
                }
                return "Halo! Selamat datang di HealthCare Plus. Saya siap membantu Anda dengan informasi kesehatan dan layanan kami. Ada yang bisa saya bantu?";

            default:
                return "Terima kasih atas pertanyaan Anda. Saya akan mencoba memberikan informasi yang tepat. Jika ada hal yang tidak jelas, jangan ragu untuk bertanya lagi atau hubungi (021) 1234-5678.";
        }
    }

    private function getSymptomAdvice(string $symptom): string
    {
        $adviceMap = [
            'demam' => 'Untuk demam, istirahat yang cukup, minum banyak air, dan kompres hangat. Jika demam >38.5°C atau berlangsung >3 hari, segera konsultasi dokter.',
            'batuk' => 'Untuk batuk, minum air hangat, madu, dan hindari udara dingin. Jika batuk berdarah atau tidak membaik dalam 2 minggu, hubungi dokter.',
            'sakit kepala' => 'Untuk sakit kepala, istirahat di tempat tenang, kompres dingin di dahi, dan hindari stress. Jika sering terjadi atau sangat parah, konsultasi dokter.',
            'mual' => 'Untuk mual, makan sedikit tapi sering, hindari makanan berlemak, minum air putih. Jika muntah terus atau dehidrasi, segera ke dokter.',
            'pilek' => 'Untuk pilek, istirahat, minum air hangat, gunakan pelembab udara. Jika disertai demam tinggi atau tidak membaik dalam 1 minggu, konsultasi dokter.'
        ];

        $advice = $adviceMap[$symptom] ?? "Untuk gejala yang Anda alami, saya sarankan konsultasi dengan dokter untuk evaluasi yang tepat.";
        
        return "🩺 {$advice}\n\n⚠️ Informasi ini hanya sebagai panduan umum. Untuk diagnosis dan pengobatan yang tepat, silakan konsultasi dengan dokter kami.\n\n📞 Hubungi (021) 1234-5678 untuk membuat janji.";
    }
}