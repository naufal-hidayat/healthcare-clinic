<?php

namespace Database\Seeders;

use App\Models\MedicalKnowledgeBase;
use Illuminate\Database\Seeder;

class MedicalKnowledgeBaseSeeder extends Seeder
{
    public function run(): void
    {
        $knowledgeBase = [
            // Gejala Umum
            [
                'category' => 'symptoms',
                'title' => 'Demam',
                'description' => 'Demam adalah peningkatan suhu tubuh di atas 37.5°C sebagai respons alami tubuh terhadap infeksi atau peradangan.',
                'keywords' => ['demam', 'panas', 'suhu tinggi', 'fever'],
                'detailed_info' => 'Demam ringan (37.5-38°C) biasanya tidak berbahaya. Demam sedang (38-39°C) perlu dipantau. Demam tinggi (>39°C) memerlukan perhatian medis.',
                'severity_level' => 'medium',
                'requires_doctor' => false,
                'recommendations' => 'Istirahat cukup, minum banyak air, kompres hangat. Jika demam >38.5°C atau berlangsung >3 hari, segera konsultasi dokter.'
            ],
            [
                'category' => 'symptoms',
                'title' => 'Batuk',
                'description' => 'Batuk adalah refleks alami tubuh untuk membersihkan saluran pernapasan dari irritan, lendir, atau benda asing.',
                'keywords' => ['batuk', 'batuk kering', 'batuk berdahak', 'cough'],
                'detailed_info' => 'Batuk kering biasanya karena iritasi. Batuk berdahak membantu mengeluarkan lendir. Batuk berdarah memerlukan perhatian medis segera.',
                'severity_level' => 'low',
                'requires_doctor' => false,
                'recommendations' => 'Minum air hangat, madu, hindari udara dingin. Jika batuk berdarah atau tidak membaik dalam 2 minggu, hubungi dokter.'
            ],
            [
                'category' => 'symptoms',
                'title' => 'Sakit Kepala',
                'description' => 'Sakit kepala adalah nyeri di area kepala atau leher yang bisa disebabkan oleh berbagai faktor.',
                'keywords' => ['sakit kepala', 'pusing', 'migrain', 'headache'],
                'detailed_info' => 'Sakit kepala tegang adalah yang paling umum. Migrain ditandai nyeri berdenyut. Sakit kepala cluster sangat menyakitkan.',
                'severity_level' => 'low',
                'requires_doctor' => false,
                'recommendations' => 'Istirahat di tempat gelap dan tenang, kompres dingin, pijat pelipis. Jika sering terjadi atau sangat parah, konsultasi dokter.'
            ],
            
            // Penyakit Umum
            [
                'category' => 'diseases',
                'title' => 'Hipertensi',
                'description' => 'Hipertensi atau tekanan darah tinggi adalah kondisi dimana tekanan darah sistolik ≥140 mmHg atau diastolik ≥90 mmHg.',
                'keywords' => ['hipertensi', 'tekanan darah tinggi', 'darah tinggi'],
                'detailed_info' => 'Hipertensi sering disebut "silent killer" karena jarang menunjukkan gejala. Dapat menyebabkan stroke, serangan jantung, dan gagal ginjal.',
                'severity_level' => 'high',
                'requires_doctor' => true,
                'recommendations' => 'Kurangi garam, olahraga teratur, kelola stress, jaga berat badan ideal. Perlu pemantauan dan pengobatan rutin oleh dokter.'
            ],
            [
                'category' => 'diseases',
                'title' => 'Diabetes Mellitus',
                'description' => 'Diabetes adalah penyakit metabolik yang ditandai dengan kadar gula darah tinggi karena gangguan produksi atau kerja insulin.',
                'keywords' => ['diabetes', 'kencing manis', 'gula darah tinggi'],
                'detailed_info' => 'Diabetes tipe 1 karena kurangnya produksi insulin. Diabetes tipe 2 karena resistensi insulin. Keduanya memerlukan pengelolaan seumur hidup.',
                'severity_level' => 'high',
                'requires_doctor' => true,
                'recommendations' => 'Diet rendah gula, olahraga teratur, monitor gula darah, minum obat sesuai anjuran dokter. Kontrol rutin sangat penting.'
            ],
            
            // Pencegahan
            [
                'category' => 'prevention',
                'title' => 'Vaksinasi Dewasa',
                'description' => 'Vaksinasi tidak hanya untuk anak-anak. Orang dewasa juga perlu vaksinasi untuk mencegah penyakit tertentu.',
                'keywords' => ['vaksin', 'imunisasi', 'vaccination'],
                'detailed_info' => 'Vaksin influenza tahunan, tetanus setiap 10 tahun, hepatitis B untuk yang berisiko, HPV untuk pencegahan kanker serviks.',
                'severity_level' => 'low',
                'requires_doctor' => false,
                'recommendations' => 'Konsultasi dengan dokter untuk menentukan vaksin yang diperlukan sesuai usia dan kondisi kesehatan.'
            ],
            [
                'category' => 'prevention',
                'title' => 'Medical Check-up Rutin',
                'description' => 'Pemeriksaan kesehatan berkala penting untuk deteksi dini penyakit dan memantau kesehatan secara keseluruhan.',
                'keywords' => ['medical checkup', 'pemeriksaan rutin', 'check up'],
                'detailed_info' => 'Usia 20-30 tahun: setiap 2-3 tahun. Usia 30-40 tahun: setiap 2 tahun. Usia >40 tahun: setiap tahun.',
                'severity_level' => 'low',
                'requires_doctor' => false,
                'recommendations' => 'Lakukan medical check-up sesuai usia. Semakin dini penyakit terdeteksi, semakin baik peluang pengobatan.'
            ],
            
            // Gizi dan Nutrisi
            [
                'category' => 'nutrition',
                'title' => 'Gizi Seimbang',
                'description' => 'Gizi seimbang adalah susunan pangan sehari-hari yang mengandung zat gizi dalam jenis dan jumlah yang sesuai kebutuhan tubuh.',
                'keywords' => ['gizi seimbang', 'nutrisi', 'makanan sehat'],
                'detailed_info' => 'Prinsip gizi seimbang: beragam, seimbang, bersih, aman. Terdiri dari karbohidrat, protein, lemak, vitamin, mineral, serat, dan air.',
                'severity_level' => 'low',
                'requires_doctor' => false,
                'recommendations' => 'Konsumsi makanan beragam, perbanyak sayur dan buah, cukup air putih, batasi gula-garam-lemak, aktif bergerak.'
            ]
        ];

        foreach ($knowledgeBase as $knowledge) {
            MedicalKnowledgeBase::create($knowledge);
        }
    }
}