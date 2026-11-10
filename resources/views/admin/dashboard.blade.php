@extends('layouts.admin', ['title' => 'Dashboard Superadmin', 'headerTitle' => 'Ikhtisar Sistem Presensi'])

@section('content')
<div class="space-y-8">

    <!-- Overview Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- Total Events -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/90 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <span class="text-xs font-medium text-slate-400 block uppercase tracking-wider">Total Event</span>
                <span class="text-2xl font-extrabold text-slate-900">{{ $totalEvents }}</span>
            </div>
        </div>

        <!-- Active Events -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/90 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <span class="text-xs font-medium text-slate-400 block uppercase tracking-wider">Event Aktif</span>
                <span class="text-2xl font-extrabold text-emerald-700">{{ $activeEvents }}</span>
            </div>
        </div>

        <!-- Total Attendees -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/90 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <div>
                <span class="text-xs font-medium text-slate-400 block uppercase tracking-wider">Total Peserta</span>
                <span class="text-2xl font-extrabold text-slate-900">{{ $totalAttendees }}</span>
            </div>
        </div>

        <!-- Total Checked-in -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/90 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                </svg>
            </div>
            <div>
                <span class="text-xs font-medium text-slate-400 block uppercase tracking-wider">Sudah Check-in</span>
                <span class="text-2xl font-extrabold text-amber-700">{{ $totalCheckedIn }}</span>
            </div>
        </div>
    </div>

    <!-- Quick Links Hub Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 text-white border border-indigo-900/40 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-indigo-500/20 text-indigo-300 flex items-center justify-center shrink-0 border border-indigo-400/20">
                <svg class="w-6 h-6 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                </svg>
            </div>
            <div>
                <h3 class="font-extrabold text-white text-base">Pusat Kumpulan Link & Navigasi Cepat</h3>
                <p class="text-xs text-indigo-200 mt-0.5">Akses cepat dan salin seluruh link formulir peserta, scanner panitia, laporan, dan pin Google Maps.</p>
            </div>
        </div>
        <div class="flex items-center gap-3 shrink-0">
            <div class="hidden lg:flex flex-col text-right pr-3 border-r border-slate-700">
                <span class="text-[11px] text-slate-400">Waktu Sistem:</span>
                <span class="text-xs font-mono font-bold text-amber-300">WIB (UTC+7)</span>
            </div>
            <a href="{{ route('admin.links') }}" 
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md shadow-indigo-600/30 transition-all cursor-pointer">
                <span>Buka Kumpulan Link</span>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </a>
        </div>
    </div>

    <!-- Events Management Section -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Daftar Event</h2>
                <p class="text-xs text-slate-500 mt-0.5">Kelola formulir, akses scanner check-in panitia, dan rekap data setiap event.</p>
            </div>
            <a href="{{ route('admin.events.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs shadow-md shadow-indigo-600/20 transition-all self-start sm:self-auto cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Buat Event Baru</span>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 text-slate-400 uppercase tracking-wider font-semibold bg-slate-50/70">
                        <th class="py-3.5 px-6">Event</th>
                        <th class="py-3.5 px-6">Jadwal & Lokasi</th>
                        <th class="py-3.5 px-6 text-center">Status</th>
                        <th class="py-3.5 px-6 text-center">Kehadiran</th>
                        <th class="py-3.5 px-6 text-right">Aksi & Shortcut</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentEvents as $ev)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-4 px-6">
                                <div class="font-bold text-slate-900 text-sm">{{ $ev->title }}</div>
                                <div class="text-[11px] text-slate-400 font-mono mt-0.5">Slug: {{ $ev->slug }}</div>
                            </td>
                            <td class="py-4 px-6 text-slate-600">
                                <div>{{ $ev->event_date ? $ev->event_date->format('d M Y') : 'Jadwal belum ditentukan' }}</div>
                                <div class="text-[11px] text-slate-400 truncate max-w-xs">{{ $ev->location ?? '-' }}</div>
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if($ev->is_active)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                        Ditutup
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center">
                                <span class="font-bold text-slate-900">{{ $ev->total_checked_in }}</span>
                                <span class="text-slate-400">/ {{ $ev->total_attendees }} hadir</span>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('attendance.form', ['slug' => $ev->slug]) }}" target="_blank" 
                                       title="Buka Form Presensi"
                                       class="p-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('event.checkin.panel', ['slug' => $ev->slug]) }}" target="_blank"
                                       title="Buka Scanner Check-in Panitia" 
                                       class="p-2 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 transition-colors">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.events.attendances.index', ['event' => $ev->id]) }}" 
                                       title="Kelola Data Kehadiran & Ekspor"
                                       class="px-3 py-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs transition-colors">
                                        Data Peserta
                                    </a>
                                    <a href="{{ route('admin.events.edit', ['event' => $ev->id]) }}" 
                                       class="p-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400 text-xs">
                                Belum ada event yang dibuat. Klik tombol "Buat Event Baru" di atas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Attendee Submissions -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-200">
            <h2 class="text-base font-bold text-slate-900">Registrasi Kehadiran Terbaru</h2>
            <p class="text-xs text-slate-500 mt-0.5">Daftar peserta yang baru saja mengisi formulir di seluruh event.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 text-slate-400 uppercase tracking-wider font-semibold bg-slate-50/70">
                        <th class="py-3 px-6">Waktu</th>
                        <th class="py-3 px-6">Nama Peserta</th>
                        <th class="py-3 px-6">Event</th>
                        <th class="py-3 px-6">Instansi & Jabatan</th>
                        <th class="py-3 px-6 text-center">Status</th>
                        <th class="py-3 px-6 text-center">Check-in</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentAttendances as $att)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3 px-6 text-slate-400 font-mono">
                                {{ $att->attended_at ? $att->attended_at->diffForHumans() : '-' }}
                            </td>
                            <td class="py-3 px-6 font-bold text-slate-900">
                                {{ $att->full_name }}
                            </td>
                            <td class="py-3 px-6 text-slate-700 font-medium">
                                {{ $att->event->title ?? '-' }}
                            </td>
                            <td class="py-3 px-6 text-slate-600">
                                <div>{{ $att->institution }}</div>
                                <div class="text-[11px] text-slate-400">{{ $att->position }}</div>
                            </td>
                            <td class="py-3 px-6 text-center">
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700">
                                    {{ $att->status_label }}
                                </span>
                            </td>
                            <td class="py-3 px-6 text-center">
                                @if($att->is_checked_in)
                                    <span class="text-emerald-700 font-bold">Sudah ({{ $att->checked_in_at ? $att->checked_in_at->format('H:i') : '' }})</span>
                                @else
                                    <span class="text-slate-400 font-medium">Belum</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400 text-xs">
                                Belum ada peserta yang mengisi kehadiran.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

