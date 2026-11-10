<?php

namespace App\Services;

use App\Models\Attendance;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MailketingService
{
    protected ?string $apiKey;

    protected ?string $apiToken;

    protected string $endpoint;

    protected string $fromName;

    protected string $fromEmail;

    public function __construct()
    {
        $this->apiKey = config('services.mailketing.api_key');
        $this->apiToken = config('services.mailketing.api_token');
        $this->endpoint = config('services.mailketing.endpoint', 'https://api.mailketing.co.id/api/v1/send');
        $this->fromName = config('services.mailketing.from_name', 'Panitia Event');
        $this->fromEmail = config('services.mailketing.from_email', 'noreply@event.com');
    }

    /**
     * Send attendance confirmation with QR code and check-in details.
     *
     * @return array{success: bool, message: string, response: mixed}
     */
    public function sendAttendanceConfirmation(Attendance $attendance, string $qrDataUri): array
    {
        if (empty($this->apiKey) && empty($this->apiToken)) {
            Log::info("Mailketing API Key belum dikonfigurasi di .env. Email simulasi dikirim ke {$attendance->email}");

            // Mark as simulated success so the flow is seamless
            $attendance->update([
                'email_sent' => false,
                'mailketing_message_id' => 'simulated-'.now()->timestamp,
            ]);

            return [
                'success' => false,
                'message' => 'Mailketing API key is not configured in .env',
                'response' => null,
            ];
        }

        $event = $attendance->event;
        $htmlContent = view('emails.attendance_ticket', [
            'attendance' => $attendance,
            'event' => $event,
            'qrDataUri' => $qrDataUri,
        ])->render();

        try {
            $payload = [
                'api_key' => $this->apiKey ?? $this->apiToken,
                'recipient' => $attendance->email,
                'to' => $attendance->email,
                'subject' => "Bukti Kehadiran & E-Tiket: {$event->title}",
                'content' => $htmlContent,
                'html' => $htmlContent,
                'from_name' => $this->fromName,
                'from_email' => $this->fromEmail,
            ];

            $response = Http::timeout(10)->post($this->endpoint, $payload);

            if ($response->successful()) {
                $data = $response->json();
                $attendance->update([
                    'email_sent' => true,
                    'email_sent_at' => now(),
                    'mailketing_message_id' => $data['message_id'] ?? $data['id'] ?? (string) now()->timestamp,
                ]);

                return [
                    'success' => true,
                    'message' => 'Email konfirmasi berhasil dikirim via Mailketing.',
                    'response' => $data,
                ];
            }

            Log::error("Mailketing send failed: {$response->status()} - {$response->body()}");

            return [
                'success' => false,
                'message' => 'Gagal mengirim email: '.$response->body(),
                'response' => $response->json(),
            ];
        } catch (\Throwable $e) {
            Log::error('Mailketing exception: '.$e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'response' => null,
            ];
        }
    }
}
