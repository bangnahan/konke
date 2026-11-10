<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Superadmin User as requested
        $superadmin = User::updateOrCreate(
            ['email' => 'bangnahan@gmail.com'],
            [
                'name' => 'Bang Nahan',
                'password' => Hash::make('pekanbaru12'),
                'role' => 'superadmin',
                'email_verified_at' => now(),
            ]
        );

        // 2. Create Panitia / Committee User (Optional alternative login for check-in staff)
        $committee = User::updateOrCreate(
            ['email' => 'panitia@event.com'],
            [
                'name' => 'Panitia Lapangan',
                'password' => Hash::make('panitia123'),
                'role' => 'committee',
                'email_verified_at' => now(),
            ]
        );

        // 3. Create Sample Event
        $event = Event::updateOrCreate(
            ['slug' => 'seminar-nasional-teknologi-2026'],
            [
                'uuid' => (string) Str::uuid(),
                'title' => 'Seminar Nasional Transformasi Digital 2026',
                'description' => 'Seminar dan temu wicara akselerasi implementasi teknologi dan kepemimpinan digital Indonesia.',
                'location' => 'Grand Ballroom Hotel Arya Duta, Pekanbaru',
                'event_date' => now()->addDays(3)->toDateString(),
                'start_time' => '08:30:00',
                'end_time' => '15:30:00',
                'access_password' => Hash::make('panitia123'), // Password akses rekap / panel panitia
                'is_active' => true,
                'allow_duplicate_submission' => false,
                'created_by' => $superadmin->id,
            ]
        );

        // 4. Sample Attendances
        $sampleAttendees = [
            [
                'full_name' => 'Ahmad Fauzan, S.Kom',
                'email' => 'fauzan.tech@gmail.com',
                'phone_number' => '081234567890',
                'institution' => 'Dinas Komunikasi dan Informatika',
                'position' => 'Kepala Bidang TI',
                'attendance_status' => 'hadir',
                'is_checked_in' => true,
                'checked_in_at' => now()->subHours(1),
            ],
            [
                'full_name' => 'Siti Nurhaliza, M.Cs',
                'email' => 'siti.nurhaliza@universitas.ac.id',
                'phone_number' => '082198765432',
                'institution' => 'Universitas Riau',
                'position' => 'Dosen Teknik Informatika',
                'attendance_status' => 'hadir',
                'is_checked_in' => false,
            ],
            [
                'full_name' => 'Budi Prasetyo',
                'email' => 'budi.prasetyo@startup.id',
                'phone_number' => '085711223344',
                'institution' => 'PT Solusi Digital Nusantara',
                'position' => 'Senior Software Engineer',
                'attendance_status' => 'hadir_terlambat',
                'is_checked_in' => false,
            ],
            [
                'full_name' => 'Rina Kartika Sari',
                'email' => 'rina.kartika@yayasan.org',
                'phone_number' => '081399887766',
                'institution' => 'Yayasan Pendidikan Riau',
                'position' => 'Staff Administrasi',
                'attendance_status' => 'tidak_hadir',
                'is_checked_in' => false,
            ],
        ];

        foreach ($sampleAttendees as $attendeeData) {
            Attendance::updateOrCreate(
                [
                    'event_id' => $event->id,
                    'email' => $attendeeData['email'],
                ],
                array_merge($attendeeData, [
                    'uuid' => (string) Str::uuid(),
                    'qr_code_token' => 'EVT-'.strtoupper(Str::random(12)),
                    'ip_address' => '127.0.0.1',
                    'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X)',
                    'attended_at' => now()->subMinutes(rand(10, 180)),
                ])
            );
        }
    }
}
