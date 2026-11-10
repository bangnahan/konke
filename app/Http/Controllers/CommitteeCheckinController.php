<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Event;
use App\Models\EventAccessLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class CommitteeCheckinController extends Controller
{
    /**
     * Show password login for committee check-in panel.
     */
    public function showLogin(string $slug): View|RedirectResponse
    {
        $event = Event::where('slug', $slug)->firstOrFail();

        $sessionKey = 'event_passcode_'.$event->id;
        if (session()->has($sessionKey) || (auth()->check() && auth()->user()->isSuperAdmin())) {
            return redirect()->route('event.checkin.panel', ['slug' => $slug]);
        }

        return view('committee.login', [
            'event' => $event,
        ]);
    }

    /**
     * Authenticate event passcode.
     */
    public function authenticate(Request $request, string $slug): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        $event = Event::where('slug', $slug)->firstOrFail();

        if (Hash::check($request->password, $event->access_password)) {
            $sessionKey = 'event_passcode_'.$event->id;
            session([$sessionKey => true]);

            EventAccessLog::create([
                'event_id' => $event->id,
                'user_id' => auth()->id(),
                'ip_address' => $request->ip(),
                'action' => 'checkin_login_success',
                'details' => 'Panitia berhasil login ke panel check-in.',
            ]);

            return redirect()->route('event.checkin.panel', ['slug' => $slug])
                ->with('success', 'Selamat datang di Panel Check-in Panitia.');
        }

        EventAccessLog::create([
            'event_id' => $event->id,
            'user_id' => auth()->id(),
            'ip_address' => $request->ip(),
            'action' => 'checkin_login_failed',
            'details' => 'Percobaan password check-in keliru dari IP '.$request->ip(),
        ]);

        return back()->withInput()->with('error', 'Password event yang Anda masukkan salah.');
    }

    /**
     * Logout from event committee session.
     */
    public function logout(string $slug): RedirectResponse
    {
        $event = Event::where('slug', $slug)->firstOrFail();
        session()->forget('event_passcode_'.$event->id);

        return redirect()->route('event.checkin.login', ['slug' => $slug])
            ->with('info', 'Sesi panitia telah diakhiri.');
    }

    /**
     * Dedicated mobile & desktop scanner panel for committee.
     */
    public function panel(string $slug): View
    {
        $event = Event::where('slug', $slug)->firstOrFail();

        $attendances = $event->attendances;
        $totalRegistered = $attendances->count();
        $totalCheckedIn = $attendances->where('is_checked_in', true)->count();
        $totalPending = $totalRegistered - $totalCheckedIn;

        $recentCheckins = $event->attendances()
            ->where('is_checked_in', true)
            ->latest('checked_in_at')
            ->take(15)
            ->get();

        return view('committee.panel', [
            'event' => $event,
            'totalRegistered' => $totalRegistered,
            'totalCheckedIn' => $totalCheckedIn,
            'totalPending' => $totalPending,
            'recentCheckins' => $recentCheckins,
        ]);
    }

    /**
     * AJAX endpoint for QR code scan processing.
     */
    public function scan(Request $request, string $slug): JsonResponse
    {
        $request->validate([
            'token' => ['required', 'string'],
        ]);

        $event = Event::where('slug', $slug)->firstOrFail();
        $token = trim($request->token);

        // Search attendance by QR token, UUID, or phone number/email as fallback
        $attendance = Attendance::where('event_id', $event->id)
            ->where(function ($query) use ($token) {
                $query->where('qr_code_token', $token)
                    ->orWhere('uuid', $token)
                    ->orWhere('phone_number', $token);
            })
            ->first();

        if (! $attendance) {
            return response()->json([
                'success' => false,
                'status' => 'not_found',
                'message' => 'Data peserta tidak ditemukan untuk event ini. Pastikan QR Code sesuai.',
            ], 404);
        }

        if ($attendance->is_checked_in) {
            return response()->json([
                'success' => false,
                'status' => 'already_checked_in',
                'message' => "Peserta ini SUDAH check-in pada {$attendance->checked_in_at->format('H:i:s, d M Y')}.",
                'data' => [
                    'id' => $attendance->id,
                    'full_name' => $attendance->full_name,
                    'institution' => $attendance->institution,
                    'position' => $attendance->position,
                    'status_label' => $attendance->status_label,
                    'checked_in_at' => $attendance->checked_in_at->format('H:i:s'),
                ],
            ]);
        }

        // Mark as checked in
        $attendance->update([
            'is_checked_in' => true,
            'checked_in_at' => now(),
            'checked_in_by' => auth()->id(),
            'checkin_method' => 'qr_scan',
        ]);

        EventAccessLog::create([
            'event_id' => $event->id,
            'user_id' => auth()->id(),
            'ip_address' => $request->ip(),
            'action' => 'qr_checkin',
            'details' => "Check-in berhasil untuk peserta: {$attendance->full_name} ({$attendance->qr_code_token})",
        ]);

        return response()->json([
            'success' => true,
            'status' => 'checkin_success',
            'message' => "Check-in BERHASIL: {$attendance->full_name}",
            'data' => [
                'id' => $attendance->id,
                'full_name' => $attendance->full_name,
                'email' => $attendance->email,
                'phone_number' => $attendance->phone_number,
                'institution' => $attendance->institution,
                'position' => $attendance->position,
                'status_label' => $attendance->status_label,
                'checked_in_at' => $attendance->checked_in_at->format('H:i:s'),
            ],
        ]);
    }

    /**
     * Manual toggle check-in from table.
     */
    public function toggleManual(Request $request, string $slug, int $id): JsonResponse|RedirectResponse
    {
        $event = Event::where('slug', $slug)->firstOrFail();
        $attendance = Attendance::where('event_id', $event->id)->where('id', $id)->firstOrFail();

        $newState = ! $attendance->is_checked_in;
        $attendance->update([
            'is_checked_in' => $newState,
            'checked_in_at' => $newState ? now() : null,
            'checked_in_by' => $newState ? auth()->id() : null,
            'checkin_method' => $newState ? 'manual' : null,
        ]);

        $message = $newState ? "Peserta {$attendance->full_name} berhasil di-check-in secara manual." : "Status check-in {$attendance->full_name} berhasil dibatalkan.";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_checked_in' => $newState,
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }
}
