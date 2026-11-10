<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventAccessLog;
use App\Services\ExportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class EventReportAccessController extends Controller
{
    /**
     * Show password login for event report.
     */
    public function showLogin(string $slug): View|RedirectResponse
    {
        $event = Event::where('slug', $slug)->firstOrFail();

        $sessionKey = 'event_passcode_'.$event->id;
        if (session()->has($sessionKey) || (auth()->check() && auth()->user()->isSuperAdmin())) {
            return redirect()->route('event.report.index', ['slug' => $slug]);
        }

        return view('reports.login', [
            'event' => $event,
        ]);
    }

    /**
     * Authenticate event passcode for report.
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
                'action' => 'report_login_success',
                'details' => 'Akses rekapitulasi dibuka via password event.',
            ]);

            return redirect()->route('event.report.index', ['slug' => $slug])
                ->with('success', 'Password terverifikasi. Selamat datang di rekap data kehadiran.');
        }

        EventAccessLog::create([
            'event_id' => $event->id,
            'user_id' => auth()->id(),
            'ip_address' => $request->ip(),
            'action' => 'report_login_failed',
            'details' => 'Percobaan password rekap salah dari IP '.$request->ip(),
        ]);

        return back()->withInput()->with('error', 'Password yang Anda masukkan tidak sesuai.');
    }

    /**
     * View password-protected report dashboard for this specific event.
     */
    public function index(Request $request, string $slug): View
    {
        $event = Event::where('slug', $slug)->firstOrFail();

        $query = $event->attendances();

        // Search filter
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%")
                    ->orWhere('institution', 'like', "%{$search}%")
                    ->orWhere('position', 'like', "%{$search}%")
                    ->orWhere('qr_code_token', 'like', "%{$search}%");
            });
        }

        // Attendance status filter
        if ($status = $request->query('status')) {
            $query->where('attendance_status', $status);
        }

        // Check-in filter
        if ($request->has('checkin') && $request->query('checkin') !== '') {
            $query->where('is_checked_in', (bool) $request->query('checkin'));
        }

        $attendees = $query->paginate(25)->withQueryString();

        // Summary counts
        $allAttendances = $event->attendances;
        $stats = [
            'total' => $allAttendances->count(),
            'hadir' => $allAttendances->where('attendance_status', 'hadir')->count(),
            'hadir_terlambat' => $allAttendances->where('attendance_status', 'hadir_terlambat')->count(),
            'tidak_hadir' => $allAttendances->where('attendance_status', 'tidak_hadir')->count(),
            'checked_in' => $allAttendances->where('is_checked_in', true)->count(),
        ];

        return view('reports.index', [
            'event' => $event,
            'attendees' => $attendees,
            'stats' => $stats,
        ]);
    }

    /**
     * Export attendees for this event.
     */
    public function export(Request $request, string $slug, ExportService $exportService): Response
    {
        $event = Event::where('slug', $slug)->firstOrFail();
        $format = strtolower($request->query('format', 'xlsx'));

        EventAccessLog::create([
            'event_id' => $event->id,
            'user_id' => auth()->id(),
            'ip_address' => $request->ip(),
            'action' => 'export_'.$format,
            'details' => "Ekspor data kehadiran format {$format}",
        ]);

        return match ($format) {
            'csv' => $exportService->exportCsv($event),
            'pdf' => $exportService->exportPdf($event),
            default => $exportService->exportXlsx($event),
        };
    }
}
