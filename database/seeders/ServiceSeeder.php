<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            // Konsultasi
            [
                'name' => 'Konsultasi Dokter Umum',
                'description' => 'Pemeriksaan dan konsultasi kesehatan umum',
                'price' => 150000,
                'duration' => '30 menit',
                'category' => 'konsultasi'
            ],
            [
                'name' => 'Konsultasi Spesialis Anak',
                'description' => 'Konsultasi kesehatan bayi, anak, dan remaja',
                'price' => 250000,
                'duration' => '45 menit',
                'category' => 'konsultasi'
            ],
            
            // Laboratorium
            [
                'name' => 'Tes Darah Lengkap',
                'description' => 'Pemeriksaan darah komprehensif',
                'price' => 200000,
                'duration' => '15 menit',
                'category' => 'laboratorium'
            ],
            [
                'name' => 'Tes Gula Darah',
                'description' => 'Pemeriksaan kadar gula dalam darah',
                'price' => 50000,
                'duration' => '10 menit',
                'category' => 'laboratorium'
            ],
            [
                'name' => 'Tes Kolesterol',
                'description' => 'Pemeriksaan kadar kolesterol',
                'price' => 75000,
                'duration' => '10 menit',
                'category' => 'laboratorium'
            ],
            
            // Medical Check-up
            [
                'name' => 'Medical Check-up Basic',
                'description' => 'Pemeriksaan kesehatan dasar',
                'price' => 500000,
                'duration' => '2 jam',
                'category' => 'checkup'
            ],
            [
                'name' => 'Medical Check-up Premium',
                'description' => 'Pemeriksaan kesehatan lengkap dan menyeluruh',
                'price' => 1200000,
                'duration' => '4 jam',
                'category' => 'checkup'
            ],
            
            // Gigi
            [
                'name' => 'Konsultasi Dokter Gigi',
                'description' => 'Pemeriksaan dan konsultasi kesehatan gigi',
                'price' => 150000,
                'duration' => '30 menit',
                'category' => 'dental'
            ],
            [
                'name' => 'Scaling Gigi',
                'description' => 'Pembersihan karang gigi',
                'price' => 300000,
                'duration' => '45 menit',
                'category' => 'dental'
            ],
            [
                'name' => 'Tambal Gigi',
                'description' => 'Perawatan gigi berlubang',
                'price' => 200000,
                'duration' => '60 menit',
                'category' => 'dental'
            ],
            
            // Farmasi
            [
                'name' => 'Konsultasi Farmasis',
                'description' => 'Konsultasi penggunaan obat yang tepat',
                'price' => 50000,
                'duration' => '15 menit',
                'category' => 'farmasi'
            ]
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}