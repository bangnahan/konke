<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Event;
use App\Services\ExportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class AttendanceController extends Controller
{
    /**
     * Display a listing of attendees for a specific event in admin panel.
     */
    public function index(Request $request, Event $event): View
    {
        $query = $event->attendances();

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

        if ($status = $request->query('status')) {
            $query->where('attendance_status', $status);
        }

        if ($request->has('checkin') && $request->query('checkin') !== '') {
            $query->where('is_checked_in', (bool) $request->query('checkin'));
        }

        $attendances = $query->paginate(20)->withQueryString();

        $all = $event->attendances;
        $stats = [
            'total' => $all->count(),
            'hadir' => $all->where('attendance_status', 'hadir')->count(),
            'hadir_terlambat' => $all->where('attendance_status', 'hadir_terlambat')->count(),
            'tidak_hadir' => $all->where('attendance_status', 'tidak_hadir')->count(),
            'checked_in' => $all->where('is_checked_in', true)->count(),
        ];

        return view('admin.attendances.index', [
            'event' => $event,
            'attendances' => $attendances,
            'stats' => $stats,
        ]);
    }

    /**
     * Toggle check-in status from admin panel.
     */
    public function toggleCheckin(Event $event, Attendance $attendance): RedirectResponse
    {
        $newState = ! $attendance->is_checked_in;
        $attendance->update([
            'is_checked_in' => $newState,
            'checked_in_at' => $newState ? now() : null,
            'checked_in_by' => $newState ? auth()->id() : null,
            'checkin_method' => $newState ? 'manual' : null,
        ]);

        return back()->with('success', "Status check-in {$attendance->full_name} berhasil diperbarui.");
    }

    /**
     * Export attendees from admin panel.
     */
    public function export(Request $request, Event $event, ExportService $exportService): Response
    {
        $format = strtolower($request->query('format', 'xlsx'));

        return match ($format) {
            'csv' => $exportService->exportCsv($event),
            'pdf' => $exportService->exportPdf($event),
            default => $exportService->exportXlsx($event),
        };
    }

    /**
     * Remove an attendee from storage.
     */
    public function destroy(Event $event, Attendance $attendance): RedirectResponse
    {
        $name = $attendance->full_name;
        $attendance->delete();

        return back()->with('success', "Data kehadiran {$name} berhasil dihapus.");
    }
}
