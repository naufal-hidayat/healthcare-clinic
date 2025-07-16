<?php

return [
    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'model' => env('OPENAI_MODEL', 'gpt-3.5-turbo'),
        'max_tokens' => 150,
        'temperature' => 0.7,
    ],
    
    'fallback_responses' => [
        'default' => 'Maaf, saya tidak dapat memahami pertanyaan Anda. Silakan hubungi (021) 1234-5678 untuk bantuan lebih lanjut.',
        'greeting' => 'Halo! Selamat datang di HealthCare Plus. Saya adalah asisten virtual yang siap membantu Anda dengan informasi seputar layanan kesehatan kami.',
        'goodbye' => 'Terima kasih telah menggunakan layanan kami. Semoga hari Anda menyenangkan!',
    ],
    
    'context_prompt' => "Anda adalah asisten virtual untuk HealthCare Plus, sebuah klinik kesehatan modern. Anda harus memberikan informasi yang akurat tentang layanan kesehatan, menjawab pertanyaan medis umum, dan membantu pasien dengan informasi klinik. Selalu berikan jawaban yang professional dan empati. Jika ada pertanyaan medis serius, arahkan untuk konsultasi langsung dengan dokter.",
];