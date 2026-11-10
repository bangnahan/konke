@extends('layouts.app', ['title' => 'E-Tiket & Bukti Kehadiran - ' . $event->title, 'event' => $event])

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6">

    <!-- Success Greeting Banner -->
    <div class="text-center mb-8">
        <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-sm">
            <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
            </svg>
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Presensi Berhasil Dicatat!</h1>
        <p class="text-slate-500 text-sm mt-1 max-w-md mx-auto">
            Terima kasih telah mengisi formulir. Bawa dan tunjukkan QR Code di bawah kepada panitia saat check-in di lokasi acara.
        </p>
    </div>

    <!-- E-Ticket Card (Printable & Screenshot-friendly) -->
    <div id="ticket-card" class="bg-white rounded-3xl border border-slate-200/90 shadow-xl overflow-hidden relative">
        <!-- Top Ticket Header -->
        <div class="bg-gradient-to-r from-indigo-700 via-indigo-800 to-slate-900 p-6 text-white text-center relative overflow-hidden">
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md text-indigo-100 text-[11px] font-semibold uppercase tracking-wider mb-2">
                <span>E-TIKET KEHADIRAN RESMI</span>
            </div>
            <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-white mb-1">{{ $event->title }}</h2>
            <p class="text-xs text-indigo-200">{{ $event->event_date ? $event->event_date->format('l, d F Y') : 'Jadwal Ditentukan Panitia' }}</p>
            @if($event->location)
                <p class="text-xs text-indigo-200 mt-1 flex items-center justify-center gap-1">
                    <svg class="w-3.5 h-3.5 text-indigo-300 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>{{ $event->location }}</span>
                </p>
            @endif
            @if($event->maps_url)
                <div class="mt-3">
                    <a href="{{ $event->maps_url }}" 
                       target="_blank" 
                       rel="noopener noreferrer" 
                       class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-white/15 hover:bg-white/25 text-white text-xs font-semibold backdrop-blur-sm transition-all border border-white/20 shadow-xs">
                        <svg class="w-3.5 h-3.5 text-amber-300" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                        </svg>
                        <span>Petunjuk Arah Google Maps &rarr;</span>
                    </a>
                </div>
            @endif
        </div>

        <!-- Ticket Body with QR Code -->
        <div class="p-6 sm:p-8 flex flex-col items-center">
            <!-- QR Code Box -->
            <div class="p-4 bg-slate-50 border-2 border-dashed border-indigo-200 rounded-2xl shadow-inner mb-4 flex flex-col items-center">
                <img src="{{ $qrDataUri }}" alt="QR Code Kehadiran" class="w-56 h-56 sm:w-64 sm:h-64 object-contain">
                <div class="mt-3 text-center">
                    <span class="text-[11px] font-medium text-slate-400 uppercase tracking-widest block">Token Check-in</span>
                    <span class="font-mono text-base font-bold text-indigo-900 tracking-wider bg-white px-3 py-1 rounded-lg border border-slate-200 inline-block mt-0.5">
                        {{ $attendance->qr_code_token }}
                    </span>
                </div>
            </div>

            <!-- Ticket Perforated Divider (Design accent) -->
            <div class="w-full flex items-center justify-between my-4 relative">
                <div class="w-6 h-6 rounded-full bg-slate-50 -ml-9 sm:-ml-11 border-r border-slate-200"></div>
                <div class="flex-1 border-b-2 border-dashed border-slate-200 mx-2"></div>
                <div class="w-6 h-6 rounded-full bg-slate-50 -mr-9 sm:-mr-11 border-l border-slate-200"></div>
            </div>

            <!-- Attendee Details Grid -->
            <div class="w-full grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm mt-2">
                <div class="p-3 bg-slate-50/70 rounded-xl border border-slate-100">
                    <span class="block text-xs text-slate-400 font-medium">Nama Lengkap</span>
                    <span class="font-semibold text-slate-900">{{ $attendance->full_name }}</span>
                </div>

                <div class="p-3 bg-slate-50/70 rounded-xl border border-slate-100">
                    <span class="block text-xs text-slate-400 font-medium">Nomor WhatsApp</span>
                    <span class="font-semibold text-slate-900">{{ $attendance->phone_number }}</span>
                </div>

                <div class="p-3 bg-slate-50/70 rounded-xl border border-slate-100">
                    <span class="block text-xs text-slate-400 font-medium">Instansi (Wajihah)</span>
                    <span class="font-semibold text-slate-900">{{ $attendance->institution }}</span>
                </div>

                <div class="p-3 bg-slate-50/70 rounded-xl border border-slate-100">
                    <span class="block text-xs text-slate-400 font-medium">Jabatan (Amanah)</span>
                    <span class="font-semibold text-slate-900">{{ $attendance->position }}</span>
                </div>

                <div class="p-3 bg-slate-50/70 rounded-xl border border-slate-100 sm:col-span-2 flex items-center justify-between">
                    <div>
                        <span class="block text-xs text-slate-400 font-medium">Status Konfirmasi</span>
                        <span class="font-semibold text-slate-900">{{ $attendance->status_label }}</span>
                    </div>
                    <div>
                        @if($attendance->is_checked_in)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                                SUDAH CHECK-IN
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                <span class="w-2 h-2 rounded-full bg-amber-600 animate-ping"></span>
                                Menunggu Check-in
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Mailketing Notification Badge -->
            <div class="w-full mt-5 p-3.5 rounded-xl bg-blue-50 border border-blue-200/80 flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <div class="text-xs text-blue-900 leading-relaxed">
                    Salinan bukti kehadiran dan QR Code telah diteruskan ke alamat email Anda (<span class="font-semibold">{{ $attendance->email }}</span>) melalui layanan <strong>Mailketing</strong>.
                </div>
            </div>
        </div>

        <!-- Action Buttons (Print, Download & Maps) -->
        <div class="p-6 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row gap-3 justify-center items-stretch sm:items-center">
            @if($event->maps_url)
                <a href="{{ $event->maps_url }}" 
                   target="_blank" 
                   rel="noopener noreferrer" 
                   class="btn-no-print inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-semibold text-sm shadow-md transition-all cursor-pointer">
                    <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                    </svg>
                    <span>Petunjuk Lokasi (Maps)</span>
                </a>
            @endif

            <a href="{{ route('attendance.qr.download', ['slug' => $event->slug, 'uuid' => $attendance->uuid]) }}" 
               class="btn-no-print inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm shadow-md shadow-indigo-600/20 transition-all cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                <span>Unduh Gambar QR Code (SVG)</span>
            </a>

            <button onclick="window.print()" 
                    class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-white hover:bg-slate-100 text-slate-800 border border-slate-200 font-semibold text-sm shadow-xs transition-all cursor-pointer">
                <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <span>Cetak / Simpan Tiket (PDF)</span>
            </button>
        </div>
    </div>

    <!-- Back to form link -->
    <div class="text-center mt-6">
        <a href="{{ route('attendance.form', ['slug' => $event->slug]) }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Kembali ke Halaman Formulir</span>
        </a>
    </div>

</div>

<!-- Print Styles -->
<style>
@media print {
    header, footer, .btn-no-print {
        display: none !important;
    }
    body {
        background: white !important;
    }
    #ticket-card {
        border: 1px solid #ccc !important;
        box-shadow: none !important;
    }
}
</style>
@endsection

