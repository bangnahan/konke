@extends('layouts.admin', ['title' => 'Kumpulan Link Penting & Navigasi', 'headerTitle' => 'Kumpulan Link Penting & Navigasi'])

@section('content')
<div class="space-y-8" x-data="{ 
    toastMessage: '', 
    toastVisible: false,
    copy(text, label) {
        navigator.clipboard.writeText(text).then(() => {
            this.toastMessage = label + ' berhasil disalin ke clipboard!';
            this.toastVisible = true;
            setTimeout(() => { this.toastVisible = false }, 3000);
        });
    }
}">

    <!-- Top Info & System Timezone Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden border border-indigo-900/40">
        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="max-w-xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/20 text-indigo-300 text-xs font-semibold mb-3 border border-indigo-400/20">
                    <svg class="w-3.5 h-3.5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd" />
                    </svg>
                    <span>Pusat Distribusi Link & Navigasi Cepat</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Kumpulan Link Penting Sistem</h1>
                <p class="text-slate-300 text-xs sm:text-sm mt-2 leading-relaxed">
                    Halaman ini mengumpulkan seluruh tautan penting (formulir publik, scanner kamera panitia, rekap data kehadiran, hingga Google Maps) agar Anda mudah menyalin dan membagikannya ke WhatsApp grup panitia maupun peserta.
                </p>
            </div>

            <!-- Live WIB Time Card -->
            <div class="bg-white/10 backdrop-blur-md p-4 sm:p-5 rounded-2xl border border-white/15 flex items-center gap-4 shrink-0"
                 x-data="{ 
                     clock: '{{ now()->format('H:i:s') }}',
                     date: '{{ now()->translatedFormat('l, d F Y') }}'
                 }"
                 x-init="setInterval(() => {
                     const d = new Date();
                     const utc = d.getTime() + (d.getTimezoneOffset() * 60000);
                     const wib = new Date(utc + (3600000 * 7));
                     clock = wib.toTimeString().split(' ')[0];
                 }, 1000)">
                <div class="w-12 h-12 rounded-xl bg-amber-400/20 text-amber-300 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xl sm:text-2xl font-mono font-bold text-white tracking-wider" x-text="clock">{{ now()->format('H:i:s') }}</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-amber-400 text-slate-950 uppercase">WIB (UTC+7)</span>
                    </div>
                    <span class="text-xs text-indigo-200 block mt-0.5 font-medium">{{ now()->format('l, d F Y') }}</span>
                    <span class="text-[11px] text-slate-400 block font-mono">Timezone: {{ $timezone }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Events Link Hub List -->
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Daftar Link Tiap Event</h2>
                <p class="text-xs text-slate-500 mt-0.5">Salin tautan sesuai kebutuhan: Peserta, Panitia Meja Registrasi, atau Pimpinan/Stakeholder.</p>
            </div>
            <a href="{{ route('admin.events.create') }}" 
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs shadow-sm transition-all">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Buat Event Baru</span>
            </a>
        </div>

        @forelse($events as $ev)
            @php
                $formUrl = route('attendance.form', ['slug' => $ev->slug]);
                $checkinUrl = route('event.checkin.panel', ['slug' => $ev->slug]);
                $reportUrl = route('event.report.index', ['slug' => $ev->slug]);
                $waText = "Assalamu'alaikum wr. wb.\n\nKepada Yth. Bapak/Ibu Peserta *" . $ev->title . "*\n\nSilakan mengisi formulir kehadiran melalui link berikut:\n" . $formUrl . "\n\nSetelah mengisi, simpan QR Code e-tiket yang Anda dapatkan untuk ditunjukkan saat check-in di meja registrasi acara.\n\nTerima kasih.";
            @endphp
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden transition-all hover:shadow-md">
                <!-- Event Header Bar -->
                <div class="p-6 bg-slate-50/70 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <h3 class="text-base font-extrabold text-slate-900">{{ $ev->title }}</h3>
                            @if($ev->is_active)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-200 text-slate-600">
                                    Ditutup
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center gap-4 text-xs text-slate-500 mt-1.5 flex-wrap">
                            <span class="font-mono text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md font-semibold">/e/{{ $ev->slug }}</span>
                            <span>📅 {{ $ev->event_date ? $ev->event_date->format('d F Y') : 'Jadwal belum ditentukan' }}</span>
                            @if($ev->location)
                                <span>📍 {{ $ev->location }}</span>
                            @endif
                            <span class="font-medium text-slate-700">👥 {{ $ev->total_checked_in }} / {{ $ev->total_attendees }} hadir</span>
                        </div>
                    </div>

                    <!-- Direct Admin Actions -->
                    <div class="flex items-center gap-2 flex-wrap">
                        <a href="{{ route('admin.events.attendances.index', ['event' => $ev->id]) }}" 
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold shadow-xs transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <span>Data Hadir ({{ $ev->total_attendees }})</span>
                        </a>

                        <a href="{{ route('admin.events.attendances.export', ['event' => $ev->id, 'format' => 'xlsx']) }}" 
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-xs transition-colors"
                           title="Unduh Rekap Excel">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>Excel</span>
                        </a>

                        <a href="{{ route('admin.events.attendances.export', ['event' => $ev->id, 'format' => 'pdf']) }}" 
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold shadow-xs transition-colors"
                           title="Unduh Dokumen PDF">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            <span>PDF</span>
                        </a>

                        <a href="{{ route('admin.events.edit', ['event' => $ev->id]) }}" 
                           class="p-1.5 rounded-xl bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 transition-colors"
                           title="Edit Event">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- 4 Link Distribution Cards Grid -->
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    
                    <!-- 1. Form Presensi Publik (Peserta) -->
                    <div class="p-4 rounded-2xl bg-indigo-50/60 border border-indigo-100 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[10px] font-bold text-indigo-800 uppercase tracking-wider bg-indigo-100 px-2 py-0.5 rounded-md">Untuk Peserta</span>
                                <span class="text-xs">📝</span>
                            </div>
                            <h4 class="text-sm font-bold text-slate-900">Formulir Presensi</h4>
                            <p class="text-xs text-slate-500 mt-1">Link terbuka untuk disebar ke WhatsApp/media sosial agar peserta mengisi data.</p>
                            
                            <div class="mt-3 p-2 bg-white rounded-xl border border-indigo-200/60 font-mono text-[11px] text-slate-700 break-all select-all">
                                {{ $formUrl }}
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-indigo-100 flex flex-col gap-2">
                            <div class="flex items-center gap-2">
                                <button type="button" 
                                        @click="copy('{{ $formUrl }}', 'Link Formulir')"
                                        class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-xs transition-colors cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                    <span>Salin Link</span>
                                </button>
                                <a href="{{ $formUrl }}" target="_blank" 
                                   class="p-1.5 rounded-xl bg-white hover:bg-indigo-100 text-indigo-700 border border-indigo-200 transition-colors"
                                   title="Buka Formulir">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                </a>
                            </div>

                            <button type="button" 
                                    @click="copy(@js($waText), 'Teks Undangan WhatsApp')"
                                    class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 text-[11px] font-semibold transition-colors cursor-pointer">
                                <span>💬 Salin Template Undangan WA</span>
                            </button>
                        </div>
                    </div>

                    <!-- 2. Scanner Panitia (Meja Registrasi) -->
                    <div class="p-4 rounded-2xl bg-amber-50/60 border border-amber-100 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[10px] font-bold text-amber-800 uppercase tracking-wider bg-amber-100 px-2 py-0.5 rounded-md">Untuk Panitia</span>
                                <span class="text-xs">📷</span>
                            </div>
                            <h4 class="text-sm font-bold text-slate-900">Scanner Check-in</h4>
                            <p class="text-xs text-slate-500 mt-1">Diberikan ke panitia meja registrasi di venue untuk scan QR tiket peserta.</p>
                            
                            <div class="mt-3 p-2 bg-white rounded-xl border border-amber-200/60 font-mono text-[11px] text-slate-700 break-all select-all">
                                {{ $checkinUrl }}
                            </div>
                            <div class="mt-2 text-[11px] text-amber-800 font-medium flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                <span>Perlu password akses event</span>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-amber-100 flex items-center gap-2">
                            <button type="button" 
                                    @click="copy('{{ $checkinUrl }}', 'Link Scanner Panitia')"
                                    class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold shadow-xs transition-colors cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                <span>Salin Link</span>
                            </button>
                            <a href="{{ $checkinUrl }}" target="_blank" 
                               class="p-1.5 rounded-xl bg-white hover:bg-amber-100 text-amber-800 border border-amber-200 transition-colors"
                               title="Buka Scanner">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- 3. Rekap Data & Laporan (Stakeholder / Pimpinan) -->
                    <div class="p-4 rounded-2xl bg-blue-50/60 border border-blue-100 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[10px] font-bold text-blue-800 uppercase tracking-wider bg-blue-100 px-2 py-0.5 rounded-md">Untuk Pimpinan</span>
                                <span class="text-xs">📊</span>
                            </div>
                            <h4 class="text-sm font-bold text-slate-900">Rekapitulasi Data</h4>
                            <p class="text-xs text-slate-500 mt-1">Diberikan ke PIC/Pimpinan yang ingin memantau dan mendownload rekap kehadiran.</p>
                            
                            <div class="mt-3 p-2 bg-white rounded-xl border border-blue-200/60 font-mono text-[11px] text-slate-700 break-all select-all">
                                {{ $reportUrl }}
                            </div>
                            <div class="mt-2 text-[11px] text-blue-800 font-medium flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-blue-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                <span>Perlu password akses event</span>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-blue-100 flex items-center gap-2">
                            <button type="button" 
                                    @click="copy('{{ $reportUrl }}', 'Link Rekapitulasi Data')"
                                    class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-xs transition-colors cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                <span>Salin Link</span>
                            </button>
                            <a href="{{ $reportUrl }}" target="_blank" 
                               class="p-1.5 rounded-xl bg-white hover:bg-blue-100 text-blue-800 border border-blue-200 transition-colors"
                               title="Buka Laporan">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- 4. Navigasi Google Maps (Venue) -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[10px] font-bold text-slate-700 uppercase tracking-wider bg-slate-200 px-2 py-0.5 rounded-md">Navigasi Lokasi</span>
                                <span class="text-xs">📍</span>
                            </div>
                            <h4 class="text-sm font-bold text-slate-900">Pin Google Maps</h4>
                            <p class="text-xs text-slate-500 mt-1">{{ $ev->location ?? 'Lokasi belum diatur' }}</p>
                            
                            @if($ev->maps_url)
                                <div class="mt-3 p-2 bg-white rounded-xl border border-slate-200 font-mono text-[11px] text-slate-700 truncate select-all" title="{{ $ev->maps_url }}">
                                    {{ $ev->maps_url }}
                                </div>
                            @else
                                <div class="mt-3 p-2 bg-amber-50 rounded-xl border border-amber-200 text-[11px] text-amber-800">
                                    Belum ada link Google Maps.
                                </div>
                            @endif
                        </div>

                        <div class="mt-4 pt-3 border-t border-slate-200 flex items-center gap-2">
                            @if($ev->maps_url)
                                <button type="button" 
                                        @click="copy('{{ $ev->maps_url }}', 'Link Google Maps')"
                                        class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold shadow-xs transition-colors cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                    <span>Salin Maps</span>
                                </button>
                                <a href="{{ $ev->maps_url }}" target="_blank" rel="noopener noreferrer" 
                                   class="p-1.5 rounded-xl bg-white hover:bg-slate-100 text-slate-800 border border-slate-200 transition-colors"
                                   title="Buka di Google Maps">
                                    <svg class="w-4 h-4 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                            @else
                                <a href="{{ route('admin.events.edit', ['event' => $ev->id]) }}" 
                                   class="w-full inline-flex items-center justify-center gap-1 px-3 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-semibold border border-indigo-200 transition-colors">
                                    <span>+ Tambahkan Link Maps</span>
                                </a>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        @empty
            <div class="bg-white rounded-3xl p-12 text-center border border-slate-200">
                <p class="text-sm text-slate-500">Belum ada event yang dibuat.</p>
                <a href="{{ route('admin.events.create') }}" class="inline-flex items-center gap-2 mt-4 px-4 py-2 rounded-xl bg-indigo-600 text-white text-xs font-bold">
                    <span>Buat Event Pertama</span>
                </a>
            </div>
        @endforelse
    </div>

    <!-- System & Security Reference Card -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-6">
        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
            <span>Informasi Keamanan & Konfigurasi Sistem</span>
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                <span class="block text-slate-400 font-semibold mb-1">Path Login Superadmin (Rahasia):</span>
                <span class="font-mono text-indigo-700 font-bold bg-white px-2.5 py-1 rounded-md border border-slate-200 inline-block">/adminganteng/login</span>
                <p class="text-[11px] text-slate-500 mt-1.5">Path /admin default disamarkan dengan status 404 Not Found demi keamanan.</p>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                <span class="block text-slate-400 font-semibold mb-1">Zona Waktu Sistem:</span>
                <span class="font-mono text-emerald-700 font-bold bg-white px-2.5 py-1 rounded-md border border-slate-200 inline-block">Asia/Jakarta (WIB, UTC+7)</span>
                <p class="text-[11px] text-slate-500 mt-1.5">Semua waktu pendaftaran dan scan check-in menggunakan Waktu Indonesia Barat.</p>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                <span class="block text-slate-400 font-semibold mb-1">Gateway Email Transaksional:</span>
                <span class="font-mono text-indigo-700 font-bold bg-white px-2.5 py-1 rounded-md border border-slate-200 inline-block">Mailketing API v2</span>
                <p class="text-[11px] text-slate-500 mt-1.5">Pengirim: <strong>KULWIL RIAU</strong> (hi@jelatix.com) terkirim otomatis saat peserta submit.</p>
            </div>
        </div>
    </div>

    <!-- Toast Notification Popup -->
    <div x-show="toastVisible" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-2"
         class="fixed bottom-6 right-6 z-50 bg-slate-900 text-white px-5 py-3 rounded-2xl shadow-2xl flex items-center gap-3 border border-slate-800"
         style="display: none;">
        <div class="w-6 h-6 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
            </svg>
        </div>
        <span class="text-xs font-semibold" x-text="toastMessage"></span>
    </div>

</div>
@endsection

