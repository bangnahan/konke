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
        $this->apiToken = config('services.mailketing.api_token') ?: $this->apiKey;
        $this->endpoint = config('services.mailketing.endpoint', 'https://api.mailketing.co.id/api/v2/send');
        $this->fromName = config('services.mailketing.from_name', 'KULWIL RIAU');
        $this->fromEmail = config('services.mailketing.from_email', 'hi@jelatix.com');
    }

    /**
     * Send attendance confirmation with QR code and check-in details via Mailketing API v2.
     *
     * @return array{success: bool, message: string, response: mixed}
     */
    public function sendAttendanceConfirmation(Attendance $attendance, string $qrDataUri): array
    {
        $token = $this->apiToken ?? $this->apiKey;

        if (empty($token)) {
            Log::info("Mailketing API Token belum dikonfigurasi di .env. Email simulasi dikirim ke {$attendance->email}");

            $attendance->update([
                'email_sent' => false,
                'mailketing_message_id' => 'simulated-'.now()->timestamp,
            ]);

            return [
                'success' => false,
                'message' => 'Mailketing API token is not configured in .env',
                'response' => null,
            ];
        }

        $event = $attendance->event;

        // Use a public HTTPS PNG QR URL because Gmail & major email clients block inline Base64 data URIs and SVGs
        $qrImageUrl = 'https://quickchart.io/qr?text='.urlencode($attendance->qr_code_token).'&size=260&format=png&margin=1';

        $htmlContent = view('emails.attendance_ticket', [
            'attendance' => $attendance,
            'event' => $event,
            'qrImageUrl' => $qrImageUrl,
            'qrDataUri' => $qrDataUri,
        ])->render();

        try {
            $payload = [
                'from_name' => $this->fromName,
                'from_email' => $this->fromEmail,
                'subject' => "Bukti Kehadiran & E-Tiket: {$event->title}",
                'recipient' => $attendance->email,
                'content' => $htmlContent,
                'api_token' => $token,
            ];

            $response = Http::timeout(15)
                ->withHeaders([
                    'X-Api-Token' => $token,
                    'Authorization' => 'Bearer '.$token,
                    'Accept' => 'application/json',
                ])
                ->post($this->endpoint, $payload);

            if ($response->successful()) {
                $data = $response->json();
                $messageId = $data['message_id'] ?? $data['id'] ?? $data['data']['message_id'] ?? (string) now()->timestamp;

                $attendance->update([
                    'email_sent' => true,
                    'email_sent_at' => now(),
                    'mailketing_message_id' => (string) $messageId,
                ]);

                Log::info("Mailketing v2 success for {$attendance->email}: message_id={$messageId}");

                return [
                    'success' => true,
                    'message' => 'Email konfirmasi berhasil dikirim via Mailketing v2.',
                    'response' => $data,
                ];
            }

            Log::error("Mailketing v2 send failed [{$response->status()}]: {$response->body()}");

            return [
                'success' => false,
                'message' => 'Gagal mengirim email: '.$response->body(),
                'response' => $response->json(),
            ];
        } catch (\Throwable $e) {
            Log::error('Mailketing v2 exception: '.$e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'response' => null,
            ];
        }
    }
}
