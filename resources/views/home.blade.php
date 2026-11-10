@extends('layouts.app', ['title' => 'Pilih Event Presensi'])

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-6 sm:py-12">
    <!-- Hero Greeting Header -->
    <div class="text-center mb-8 sm:mb-12">
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-indigo-50 border border-indigo-200/80 text-indigo-700 text-xs font-semibold mb-3.5 shadow-2xs">
            <span class="w-2 h-2 rounded-full bg-indigo-600 animate-pulse"></span>
            <span>Portal Presensi Digital</span>
        </div>
        <h1 class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
            Silakan Pilih Event yang Akan Anda Ikuti
        </h1>
        <p class="text-sm sm:text-base text-slate-500 mt-2.5 max-w-xl mx-auto leading-relaxed">
            Pilih salah satu acara resmi di bawah ini untuk mengisi formulir konfirmasi kehadiran dan mendapatkan QR Code e-tiket Anda.
        </p>
    </div>

    <!-- Active Events List -->
    <div class="space-y-4 sm:space-y-5">
        @forelse($events as $event)
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm hover:shadow-md transition-all p-6 sm:p-7 flex flex-col md:flex-row md:items-center justify-between gap-6 group">
                <div class="space-y-3 flex-1 min-w-0">
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Pendaftaran Dibuka
                        </span>
                        @if($event->event_date)
                            <span class="text-xs text-slate-500 font-medium">
                                📅 {{ $event->event_date->translatedFormat('l, d F Y') }}
                            </span>
                        @endif
                    </div>

                    <h2 class="text-lg sm:text-xl font-bold text-slate-900 group-hover:text-indigo-600 transition-colors">
                        <a href="{{ route('attendance.form', ['slug' => $event->slug]) }}">
                            {{ $event->title }}
                        </a>
                    </h2>

                    @if($event->description)
                        <p class="text-xs sm:text-sm text-slate-500 line-clamp-2 leading-relaxed">
                            {{ $event->description }}
                        </p>
                    @endif

                    <div class="flex flex-wrap items-center gap-4 text-xs text-slate-600 pt-1">
                        @if($event->start_time)
                            <div class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-indigo-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>{{ substr($event->start_time, 0, 5) }} {{ $event->end_time ? '- ' . substr($event->end_time, 0, 5) : '' }} WIB</span>
                            </div>
                        @endif

                        @if($event->location)
                            <div class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span class="truncate max-w-xs sm:max-w-md">{{ $event->location }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Action Button -->
                <div class="flex items-center gap-3 shrink-0 pt-3 md:pt-0 border-t md:border-t-0 border-slate-100">
                    @if($event->maps_url)
                        <a href="{{ $event->maps_url }}" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors"
                           title="Buka Navigasi Google Maps">
                            <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                            </svg>
                            <span class="hidden sm:inline">Peta</span>
                        </a>
                    @endif

                    <a href="{{ route('attendance.form', ['slug' => $event->slug]) }}" 
                       class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs sm:text-sm font-bold shadow-md shadow-indigo-600/20 transition-all group-hover:bg-indigo-700">
                        <span>Isi Presensi</span>
                        <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-3xl border border-slate-200/90 p-12 text-center shadow-xs">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-900">Belum Ada Event Aktif</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                    Saat ini belum ada event yang dibuka untuk pengisian presensi. Silakan hubungi panitia penyelenggara.
                </p>
            </div>
        @endforelse
    </div>
</div>
@endsection
