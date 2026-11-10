@extends('layouts.app', ['title' => 'Rekap Data Kehadiran - ' . $event->title, 'event' => $event])

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6">

    <!-- Header & Actions -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs mb-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                <span class="text-xs font-bold uppercase tracking-wider text-indigo-600">REKAPITULASI DATA KEHADIRAN</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">{{ $event->title }}</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                {{ $event->event_date ? $event->event_date->format('d F Y') : 'Jadwal Ditentukan' }} &bull; {{ $event->location ?? 'Lokasi belum diatur' }}
            </p>
        </div>

        <!-- Export & Panel Action Buttons -->
        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('event.checkin.panel', ['slug' => $event->slug]) }}" 
               class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-sm transition-all">
                <svg class="w-4 h-4 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                </svg>
                <span>Buka Scanner Check-in</span>
            </a>

            <!-- Export Dropdown -->
            <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                <button @click="open = !open" 
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md shadow-indigo-600/20 transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    <span>Ekspor Data</span>
                    <svg class="w-3.5 h-3.5 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="open" 
                     x-transition 
                     style="display: none;" 
                     class="absolute right-0 mt-2 w-48 bg-white rounded-2xl shadow-xl border border-slate-100 py-1.5 z-40">
                    <a href="{{ route('event.report.export', ['slug' => $event->slug, 'format' => 'xlsx']) }}" 
                       class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-indigo-600 transition-colors">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span>Excel Spreadsheet (.xlsx)</span>
                    </a>
                    <a href="{{ route('event.report.export', ['slug' => $event->slug, 'format' => 'csv']) }}" 
                       class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-indigo-600 transition-colors">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                        <span>Comma Separated (.csv)</span>
                    </a>
                    <a href="{{ route('event.report.export', ['slug' => $event->slug, 'format' => 'pdf']) }}" 
                       class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-indigo-600 transition-colors">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                        <span>Dokumen Cetak (.pdf)</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Metric Stat Badges -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 sm:gap-4 mb-8">
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-medium text-slate-400 block">Total Terdaftar</span>
            <span class="text-2xl font-extrabold text-slate-900">{{ $stats['total'] }}</span>
        </div>
        <div class="bg-emerald-50/70 p-4 rounded-2xl border border-emerald-200/80 shadow-xs">
            <span class="text-xs font-semibold text-emerald-700 block">Sudah Check-in</span>
            <span class="text-2xl font-extrabold text-emerald-800">{{ $stats['checked_in'] }}</span>
        </div>
        <div class="bg-indigo-50/70 p-4 rounded-2xl border border-indigo-200/80 shadow-xs">
            <span class="text-xs font-semibold text-indigo-700 block">Konfirmasi Hadir</span>
            <span class="text-2xl font-extrabold text-indigo-800">{{ $stats['hadir'] }}</span>
        </div>
        <div class="bg-amber-50/70 p-4 rounded-2xl border border-amber-200/80 shadow-xs">
            <span class="text-xs font-semibold text-amber-700 block">Hadir Terlambat</span>
            <span class="text-2xl font-extrabold text-amber-800">{{ $stats['hadir_terlambat'] }}</span>
        </div>
        <div class="bg-rose-50/70 p-4 rounded-2xl border border-rose-200/80 shadow-xs col-span-2 sm:col-span-1">
            <span class="text-xs font-semibold text-rose-700 block">Tidak Hadir (Izin)</span>
            <span class="text-2xl font-extrabold text-rose-800">{{ $stats['tidak_hadir'] }}</span>
        </div>
    </div>

    <!-- Filter & Table Card -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden mb-12">
        <!-- Search and Filter Header -->
        <div class="p-5 border-b border-slate-200 bg-slate-50/50">
            <form action="{{ route('event.report.index', ['slug' => $event->slug]) }}" method="GET" class="flex flex-col md:flex-row gap-3">
                <div class="flex-1 relative">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Cari nama, email, no whatsapp, instansi, jabatan, token..." 
                           class="w-full pl-10 pr-4 py-2 text-sm bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                <div class="flex gap-2">
                    <select name="status" class="text-xs bg-white border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:ring-2 focus:ring-indigo-600">
                        <option value="">Semua Status Hadir</option>
                        <option value="hadir" {{ request('status') === 'hadir' ? 'selected' : '' }}>Hadir</option>
                        <option value="hadir_terlambat" {{ request('status') === 'hadir_terlambat' ? 'selected' : '' }}>Hadir Terlambat</option>
                        <option value="tidak_hadir" {{ request('status') === 'tidak_hadir' ? 'selected' : '' }}>Tidak Hadir</option>
                    </select>

                    <select name="checkin" class="text-xs bg-white border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:ring-2 focus:ring-indigo-600">
                        <option value="">Semua Check-in</option>
                        <option value="1" {{ request('checkin') === '1' ? 'selected' : '' }}>Sudah Check-in</option>
                        <option value="0" {{ request('checkin') === '0' ? 'selected' : '' }}>Belum Check-in</option>
                    </select>

                    <button type="submit" class="px-4 py-2 bg-slate-900 text-white rounded-xl text-xs font-bold hover:bg-slate-800 transition-colors">
                        Filter
                    </button>
                    
                    @if(request()->hasAny(['search', 'status', 'checkin']))
                        <a href="{{ route('event.report.index', ['slug' => $event->slug]) }}" class="px-3 py-2 bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold hover:bg-slate-300 transition-colors flex items-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table View -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 text-slate-400 uppercase tracking-wider font-semibold bg-slate-50/70">
                        <th class="py-3 px-4">No</th>
                        <th class="py-3 px-4">Token QR</th>
                        <th class="py-3 px-4">Nama Lengkap</th>
                        <th class="py-3 px-4">Kontak</th>
                        <th class="py-3 px-4">Instansi (Wajihah)</th>
                        <th class="py-3 px-4">Jabatan (Amanah)</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Check-in</th>
                        <th class="py-3 px-4">Waktu Daftar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($attendees as $index => $item)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3.5 px-4 text-slate-400 font-mono">
                                {{ $attendees->firstItem() + $index }}
                            </td>
                            <td class="py-3.5 px-4 font-mono font-bold text-indigo-700">
                                {{ $item->qr_code_token }}
                            </td>
                            <td class="py-3.5 px-4 font-bold text-slate-900">
                                {{ $item->full_name }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-600">
                                <div>{{ $item->phone_number }}</div>
                                <div class="text-[11px] text-slate-400">{{ $item->email }}</div>
                            </td>
                            <td class="py-3.5 px-4 text-slate-700 font-medium">
                                {{ $item->institution }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-700 font-medium">
                                {{ $item->position }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($item->attendance_status === 'hadir')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Hadir
                                    </span>
                                @elseif($item->attendance_status === 'hadir_terlambat')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                        Terlambat
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                        Tidak Hadir
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($item->is_checked_in)
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span>{{ $item->checked_in_at ? $item->checked_in_at->format('H:i') : 'Ya' }}</span>
                                    </span>
                                @else
                                    <span class="text-[11px] text-slate-400 font-medium">Belum</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-slate-500 font-mono text-[11px]">
                                {{ $item->attended_at ? $item->attended_at->format('d/m H:i') : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-slate-400 text-xs">
                                Tidak ada data peserta yang cocok dengan filter atau pencarian Anda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($attendees->hasPages())
            <div class="p-4 border-t border-slate-200 bg-white">
                {{ $attendees->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

