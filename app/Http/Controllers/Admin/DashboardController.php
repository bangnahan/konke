<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Event;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display central admin dashboard overview.
     */
    public function index(): View
    {
        $totalEvents = Event::count();
        $activeEvents = Event::where('is_active', true)->count();
        $totalAttendees = Attendance::count();
        $totalCheckedIn = Attendance::where('is_checked_in', true)->count();

        $recentEvents = Event::withCount(['attendances as total_attendees', 'attendances as total_checked_in' => function ($q) {
            $q->where('is_checked_in', true);
        }])->latest()->take(6)->get();

        $recentAttendances = Attendance::with('event')
            ->latest('attended_at')
            ->take(10)
            ->get();

        return view('admin.dashboard', [
            'totalEvents' => $totalEvents,
            'activeEvents' => $activeEvents,
            'totalAttendees' => $totalAttendees,
            'totalCheckedIn' => $totalCheckedIn,
            'recentEvents' => $recentEvents,
            'recentAttendances' => $recentAttendances,
        ]);
    }

    /**
     * Display the Quick Links Hub for all events and system navigation.
     */
    public function links(): View
    {
        $events = Event::withCount(['attendances as total_attendees', 'attendances as total_checked_in' => function ($q) {
            $q->where('is_checked_in', true);
        }])->latest()->get();

        return view('admin.links', [
            'events' => $events,
            'serverTime' => now()->format('d F Y, H:i:s').' WIB',
            'timezone' => config('app.timezone'),
        ]);
    }
}
