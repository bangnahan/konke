<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class EventController extends Controller
{
    /**
     * Display a listing of events.
     */
    public function index(Request $request): View
    {
        $query = Event::withCount(['attendances', 'attendances as checked_in_count' => function ($q) {
            $q->where('is_checked_in', true);
        }]);

        if ($search = $request->query('search')) {
            $query->where('title', 'like', "%{$search}%")
                ->orWhere('location', 'like', "%{$search}%");
        }

        $events = $query->latest()->paginate(10)->withQueryString();

        return view('admin.events.index', [
            'events' => $events,
        ]);
    }

    /**
     * Show the form for creating a new event.
     */
    public function create(): View
    {
        return view('admin.events.create');
    }

    /**
     * Store a newly created event in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:events,slug'],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'event_date' => ['nullable', 'date'],
            'start_time' => ['nullable'],
            'end_time' => ['nullable'],
            'access_password' => ['required', 'string', 'min:4'],
            'banner' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:3072'],
            'is_active' => ['boolean'],
            'allow_duplicate_submission' => ['boolean'],
        ]);

        $slug = ! empty($validated['slug'])
            ? Str::slug($validated['slug'])
            : Str::slug($validated['title']).'-'.Str::lower(Str::random(5));

        $bannerPath = null;
        if ($request->hasFile('banner')) {
            $bannerPath = $request->file('banner')->store('banners', 'public');
        }

        $event = Event::create([
            'uuid' => (string) Str::uuid(),
            'title' => $validated['title'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'location' => $validated['location'] ?? null,
            'event_date' => $validated['event_date'] ?? null,
            'start_time' => $validated['start_time'] ?? null,
            'end_time' => $validated['end_time'] ?? null,
            'banner_path' => $bannerPath,
            'access_password' => Hash::make($validated['access_password']),
            'is_active' => $request->boolean('is_active', true),
            'allow_duplicate_submission' => $request->boolean('allow_duplicate_submission', false),
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.events.index')
            ->with('success', "Event '{$event->title}' berhasil dibuat!");
    }

    /**
     * Show the form for editing the specified event.
     */
    public function edit(Event $event): View
    {
        return view('admin.events.edit', [
            'event' => $event,
        ]);
    }

    /**
     * Update the specified event in storage.
     */
    public function update(Request $request, Event $event): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:events,slug,'.$event->id],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'event_date' => ['nullable', 'date'],
            'start_time' => ['nullable'],
            'end_time' => ['nullable'],
            'access_password' => ['nullable', 'string', 'min:4'],
            'banner' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:3072'],
            'is_active' => ['boolean'],
            'allow_duplicate_submission' => ['boolean'],
        ]);

        $updateData = [
            'title' => $validated['title'],
            'slug' => Str::slug($validated['slug']),
            'description' => $validated['description'] ?? null,
            'location' => $validated['location'] ?? null,
            'event_date' => $validated['event_date'] ?? null,
            'start_time' => $validated['start_time'] ?? null,
            'end_time' => $validated['end_time'] ?? null,
            'is_active' => $request->boolean('is_active'),
            'allow_duplicate_submission' => $request->boolean('allow_duplicate_submission'),
        ];

        if ($request->hasFile('banner')) {
            if ($event->banner_path && Storage::disk('public')->exists($event->banner_path)) {
                Storage::disk('public')->delete($event->banner_path);
            }
            $updateData['banner_path'] = $request->file('banner')->store('banners', 'public');
        }

        if (! empty($validated['access_password'])) {
            $updateData['access_password'] = Hash::make($validated['access_password']);
        }

        $event->update($updateData);

        return redirect()->route('admin.events.index')
            ->with('success', "Event '{$event->title}' berhasil diperbarui.");
    }

    /**
     * Remove the specified event from storage.
     */
    public function destroy(Event $event): RedirectResponse
    {
        $title = $event->title;
        if ($event->banner_path && Storage::disk('public')->exists($event->banner_path)) {
            Storage::disk('public')->delete($event->banner_path);
        }
        $event->delete();

        return redirect()->route('admin.events.index')
            ->with('success', "Event '{$title}' berhasil dihapus.");
    }
}
