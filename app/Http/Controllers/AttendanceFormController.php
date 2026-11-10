<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAttendanceRequest;
use App\Models\Attendance;
use App\Models\Event;
use App\Services\MailketingService;
use App\Services\QrCodeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;

class AttendanceFormController extends Controller
{
    /**
     * Show attendance form for an event.
     */
    public function show(string $slug): View
    {
        $event = Event::where('slug', $slug)->firstOrFail();

        return view('attendances.form', [
            'event' => $event,
        ]);
    }

    /**
     * Process attendance submission.
     */
    public function store(
        StoreAttendanceRequest $request,
        string $slug,
        QrCodeService $qrCodeService,
        MailketingService $mailketingService
    ): RedirectResponse {
        $event = Event::where('slug', $slug)->firstOrFail();

        if (! $event->is_active) {
            return back()->withInput()->with('error', 'Mohon maaf, formulir kehadiran untuk event ini telah ditutup.');
        }

        $validated = $request->validated();

        // Anti-duplication check
        if (! $event->allow_duplicate_submission) {
            $duplicate = Attendance::where('event_id', $event->id)
                ->where(function ($query) use ($validated) {
                    $query->where('email', $validated['email'])
                        ->orWhere('phone_number', $validated['phone_number']);
                })
                ->first();

            if ($duplicate) {
                return redirect()->route('attendance.success', [
                    'slug' => $event->slug,
                    'uuid' => $duplicate->uuid,
                ])->with('info', 'Anda sebelumnya sudah pernah mengisi data kehadiran ini. Berikut adalah QR Code tiket kehadiran Anda.');
            }
        }

        // Create Attendance
        $attendance = Attendance::create([
            'event_id' => $event->id,
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'institution' => $validated['institution'],
            'position' => $validated['position'],
            'attendance_status' => $validated['attendance_status'],
            'notes' => $validated['notes'] ?? null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'attended_at' => now(),
        ]);

        // Generate QR code data URI for email & instant ticket
        $qrDataUri = $qrCodeService->generateDataUri($attendance->qr_code_token);

        // Dispatch Mailketing confirmation
        $mailketingService->sendAttendanceConfirmation($attendance, $qrDataUri);

        return redirect()->route('attendance.success', [
            'slug' => $event->slug,
            'uuid' => $attendance->uuid,
        ])->with('success', 'Kehadiran Anda berhasil dicatat! Simpan QR Code di bawah untuk ditunjukkan saat check-in di lokasi acara.');
    }

    /**
     * Display success page with QR Code and Ticket.
     */
    public function success(string $slug, string $uuid, QrCodeService $qrCodeService): View
    {
        $event = Event::where('slug', $slug)->firstOrFail();
        $attendance = Attendance::where('event_id', $event->id)->where('uuid', $uuid)->firstOrFail();

        $qrDataUri = $qrCodeService->generateDataUri($attendance->qr_code_token, 7);

        return view('attendances.success', [
            'event' => $event,
            'attendance' => $attendance,
            'qrDataUri' => $qrDataUri,
        ]);
    }

    /**
     * Download QR code as SVG.
     */
    public function downloadQr(string $slug, string $uuid, QrCodeService $qrCodeService): Response
    {
        $event = Event::where('slug', $slug)->firstOrFail();
        $attendance = Attendance::where('event_id', $event->id)->where('uuid', $uuid)->firstOrFail();

        $rawSvg = $qrCodeService->generateRawSvg($attendance->qr_code_token, 8);
        $filename = 'qrcode-'.$attendance->qr_code_token.'.svg';

        return response($rawSvg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
