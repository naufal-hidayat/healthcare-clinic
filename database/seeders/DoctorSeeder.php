<?php

namespace Database\Seeders;

use App\Models\Doctor;
use Illuminate\Database\Seeder;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        $doctors = [
            [
                'name' => 'Ahmad Wijaya',
                'email' => 'dr.ahmad@healthcareplus.com',
                'phone' => '081234567890',
                'specialization' => 'Dokter Umum',
                'license_number' => 'STR001234567',
                'education' => 'S1 Kedokteran Universitas Indonesia, S2 Kesehatan Masyarakat',
                'experience' => '8 tahun pengalaman dalam pelayanan kesehatan primer',
                'schedule' => [
                    'monday' => ['08:00-12:00', '14:00-17:00'],
                    'tuesday' => ['08:00-12:00', '14:00-17:00'],
                    'wednesday' => ['08:00-12:00', '14:00-17:00'],
                    'thursday' => ['08:00-12:00', '14:00-17:00'],
                    'friday' => ['08:00-12:00', '14:00-17:00'],
                    'saturday' => ['08:00-12:00'],
                    'sunday' => ['off']
                ]
            ],
            [
                'name' => 'Sarah Putri',
                'email' => 'dr.sarah@healthcareplus.com',
                'phone' => '081234567891',
                'specialization' => 'Spesialis Anak',
                'license_number' => 'STR001234568',
                'education' => 'S1 Kedokteran, Spesialis Anak Universitas Gadjah Mada',
                'experience' => '10 tahun spesialis kesehatan anak dan remaja',
                'schedule' => [
                    'monday' => ['09:00-12:00', '14:00-16:00'],
                    'tuesday' => ['09:00-12:00', '14:00-16:00'],
                    'wednesday' => ['09:00-12:00', '14:00-16:00'],
                    'thursday' => ['09:00-12:00', '14:00-16:00'],
                    'friday' => ['09:00-12:00', '14:00-16:00'],
                    'saturday' => ['09:00-12:00'],
                    'sunday' => ['off']
                ]
            ],
            [
                'name' => 'Michael Chen',
                'email' => 'dr.michael@healthcareplus.com',
                'phone' => '081234567892',
                'specialization' => 'Dokter Gigi',
                'license_number' => 'STR001234569',
                'education' => 'S1 Kedokteran Gigi Universitas Trisakti',
                'experience' => '6 tahun dalam perawatan kesehatan gigi dan mulut',
                'schedule' => [
                    'monday' => ['08:00-12:00', '13:00-17:00'],
                    'tuesday' => ['08:00-12:00', '13:00-17:00'],
                    'wednesday' => ['08:00-12:00', '13:00-17:00'],
                    'thursday' => ['08:00-12:00', '13:00-17:00'],
                    'friday' => ['08:00-12:00', '13:00-17:00'],
                    'saturday' => ['08:00-15:00'],
                    'sunday' => ['off']
                ]
            ]
        ];

        foreach ($doctors as $doctor) {
            Doctor::create($doctor);
        }
    }
}