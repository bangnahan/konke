@extends('layouts.app', ['title' => 'Scanner Check-in Panitia - ' . $event->title, 'event' => $event])

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6" x-data="checkinScanner()">

    <!-- Panel Header -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-xs font-bold uppercase tracking-wider text-indigo-600">PANEL CHECK-IN LAPANGAN</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight mt-0.5">{{ $event->title }}</h1>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('event.report.index', ['slug' => $event->slug]) }}" 
               class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors">
                <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Lihat Rekap</span>
            </a>

            <form action="{{ route('event.checkin.logout', ['slug' => $event->slug]) }}" method="POST">
                @csrf
                <button type="submit" 
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 transition-colors cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span>Tutup Sesi</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Quick Stats Metric Cards -->
    <div class="grid grid-cols-3 gap-3 sm:gap-4 mb-6">
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 text-center shadow-xs">
            <span class="text-xs font-medium text-slate-400 block">Total Terdaftar</span>
            <span class="text-xl sm:text-2xl font-extrabold text-slate-900" x-text="stats.totalRegistered">{{ $totalRegistered }}</span>
        </div>

        <div class="bg-emerald-50/70 p-4 rounded-2xl border border-emerald-200/80 text-center shadow-xs">
            <span class="text-xs font-semibold text-emerald-700 block">Sudah Hadir</span>
            <span class="text-xl sm:text-2xl font-extrabold text-emerald-800" x-text="stats.totalCheckedIn">{{ $totalCheckedIn }}</span>
        </div>

        <div class="bg-amber-50/70 p-4 rounded-2xl border border-amber-200/80 text-center shadow-xs">
            <span class="text-xs font-semibold text-amber-700 block">Belum Check-in</span>
            <span class="text-xl sm:text-2xl font-extrabold text-amber-800" x-text="stats.totalPending">{{ $totalPending }}</span>
        </div>
    </div>

    <!-- Scanner & Manual Input Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-8">
        <!-- Camera Scanner Area (7 Cols) -->
        <div class="lg:col-span-7 bg-white rounded-3xl p-5 border border-slate-200/90 shadow-sm flex flex-col">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>Kamera Scanner QR</span>
                </span>

                <button @click="toggleScanner()" 
                        class="text-xs font-semibold px-2.5 py-1 rounded-lg transition-colors cursor-pointer"
                        :class="isScanning ? 'bg-rose-100 text-rose-700 hover:bg-rose-200' : 'bg-indigo-100 text-indigo-700 hover:bg-indigo-200'">
                    <span x-text="isScanning ? 'Jeda Kamera' : 'Nyalakan Kamera'"></span>
                </button>
            </div>

            <!-- Viewport Container for Scanner -->
            <div class="relative bg-slate-900 rounded-2xl overflow-hidden aspect-square sm:aspect-4/3 flex items-center justify-center">
                <div id="qr-reader" class="w-full h-full"></div>

                <!-- Scanner Targeting Overlay -->
                <div x-show="isScanning" class="absolute inset-0 pointer-events-none flex items-center justify-center p-8">
                    <div class="w-56 h-56 border-2 border-indigo-400/80 rounded-2xl relative shadow-lg">
                        <div class="absolute -top-1 -left-1 w-5 h-5 border-t-4 border-l-4 border-indigo-400 rounded-tl-lg"></div>
                        <div class="absolute -top-1 -right-1 w-5 h-5 border-t-4 border-r-4 border-indigo-400 rounded-tr-lg"></div>
                        <div class="absolute -bottom-1 -left-1 w-5 h-5 border-b-4 border-l-4 border-indigo-400 rounded-bl-lg"></div>
                        <div class="absolute -bottom-1 -right-1 w-5 h-5 border-b-4 border-r-4 border-indigo-400 rounded-br-lg"></div>
                        <div class="w-full h-0.5 bg-indigo-400/70 shadow-glow animate-bounce mt-28"></div>
                    </div>
                </div>

                <!-- Placeholder when camera stopped -->
                <div x-show="!isScanning" class="text-center p-6 text-slate-400">
                    <svg class="w-12 h-12 mx-auto text-slate-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    <p class="text-xs">Kamera dalam posisi tidak aktif.</p>
                    <button @click="startScanner()" class="mt-3 px-4 py-2 bg-indigo-600 text-white text-xs font-bold rounded-xl shadow-sm hover:bg-indigo-700 cursor-pointer">
                        Mulai Pindai Kamera
                    </button>
                </div>
            </div>

            <!-- Manual Input Form -->
            <div class="mt-4 pt-4 border-t border-slate-100">
                <label for="manual_token" class="block text-xs font-semibold text-slate-600 mb-1">
                    Input Manual (Token QR / No WhatsApp)
                </label>
                <form @submit.prevent="submitManualToken()" class="flex gap-2">
                    <input type="text" 
                           x-model="manualToken" 
                           id="manual_token" 
                           placeholder="Contoh: EVT-ABC123 atau 081234..." 
                           class="flex-1 px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-600">
                    <button type="submit" 
                            :disabled="processing || !manualToken" 
                            class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl transition-all disabled:opacity-50 cursor-pointer">
                        Check-in
                    </button>
                </form>
            </div>
        </div>

        <!-- Scan Result / Feedback Card (5 Cols) -->
        <div class="lg:col-span-5 flex flex-col">
            <!-- Active Result Banner -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm flex-1 flex flex-col justify-center text-center relative overflow-hidden"
                 :class="{
                    'border-emerald-300 ring-2 ring-emerald-500/20 bg-emerald-50/20': lastResult && lastResult.status === 'checkin_success',
                    'border-amber-300 ring-2 ring-amber-500/20 bg-amber-50/20': lastResult && lastResult.status === 'already_checked_in',
                    'border-rose-300 ring-2 ring-rose-500/20 bg-rose-50/20': lastResult && lastResult.status === 'not_found'
                 }">

                <template x-if="!lastResult">
                    <div class="py-12 text-slate-400">
                        <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-3 text-slate-400">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                        </div>
                        <h3 class="font-bold text-slate-700 text-sm">Menunggu Pindaian QR</h3>
                        <p class="text-xs text-slate-400 mt-1">Arahkan kamera ke QR Code peserta untuk memverifikasi kehadiran seketika.</p>
                    </div>
                </template>

                <template x-if="lastResult">
                    <div class="py-4">
                        <!-- Icon Badge -->
                        <div class="w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-3"
                             :class="{
                                'bg-emerald-100 text-emerald-600': lastResult.status === 'checkin_success',
                                'bg-amber-100 text-amber-600': lastResult.status === 'already_checked_in',
                                'bg-rose-100 text-rose-600': lastResult.status === 'not_found'
                             }">
                            <template x-if="lastResult.status === 'checkin_success'">
                                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                            </template>
                            <template x-if="lastResult.status === 'already_checked_in'">
                                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </template>
                            <template x-if="lastResult.status === 'not_found'">
                                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </template>
                        </div>

                        <!-- Status Title -->
                        <span class="text-xs font-bold uppercase tracking-wider block"
                              :class="{
                                'text-emerald-700': lastResult.status === 'checkin_success',
                                'text-amber-700': lastResult.status === 'already_checked_in',
                                'text-rose-700': lastResult.status === 'not_found'
                              }"
                              x-text="lastResult.status === 'checkin_success' ? 'CHECK-IN BERHASIL' : (lastResult.status === 'already_checked_in' ? 'SUDAH CHECK-IN SEBELUMNYA' : 'TIDAK DITEMUKAN')">
                        </span>

                        <h2 class="text-xl font-extrabold text-slate-900 mt-1 mb-1" 
                            x-text="lastResult.data ? lastResult.data.full_name : 'Data Tidak Cocok'"></h2>
                        
                        <p class="text-xs text-slate-500 mb-4" x-text="lastResult.message"></p>

                        <!-- Attendee Info Card -->
                        <template x-if="lastResult.data">
                            <div class="bg-white/90 p-4 rounded-xl border border-slate-200/80 text-left text-xs space-y-2 mb-2 shadow-xs">
                                <div class="flex justify-between border-b border-slate-100 pb-1.5">
                                    <span class="text-slate-400">Instansi (Wajihah):</span>
                                    <span class="font-bold text-slate-800" x-text="lastResult.data.institution"></span>
                                </div>
                                <div class="flex justify-between border-b border-slate-100 pb-1.5">
                                    <span class="text-slate-400">Jabatan (Amanah):</span>
                                    <span class="font-bold text-slate-800" x-text="lastResult.data.position"></span>
                                </div>
                                <div class="flex justify-between border-b border-slate-100 pb-1.5">
                                    <span class="text-slate-400">Status Kehadiran:</span>
                                    <span class="font-bold text-slate-800" x-text="lastResult.data.status_label"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-400">Waktu Check-in:</span>
                                    <span class="font-bold text-indigo-700" x-text="lastResult.data.checked_in_at"></span>
                                </div>
                            </div>
                        </template>

                        <button @click="lastResult = null" 
                                class="mt-2 text-xs font-semibold text-slate-500 hover:text-slate-800 underline cursor-pointer">
                            Tutup Notifikasi & Lanjutkan
                        </button>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Recent Check-ins Realtime Feed -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-bold text-slate-900">Riwayat Check-in Terkini</h2>
            <span class="text-xs text-slate-400">15 Peserta Terakhir</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 text-slate-400 uppercase tracking-wider font-semibold">
                        <th class="py-2.5 px-3">Waktu</th>
                        <th class="py-2.5 px-3">Nama Lengkap</th>
                        <th class="py-2.5 px-3">Instansi (Wajihah)</th>
                        <th class="py-2.5 px-3">Jabatan</th>
                        <th class="py-2.5 px-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentCheckins as $item)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3 px-3 font-mono font-medium text-indigo-600">
                                {{ $item->checked_in_at ? $item->checked_in_at->format('H:i:s') : '-' }}
                            </td>
                            <td class="py-3 px-3 font-bold text-slate-900">
                                {{ $item->full_name }}
                            </td>
                            <td class="py-3 px-3 text-slate-600">
                                {{ $item->institution }}
                            </td>
                            <td class="py-3 px-3 text-slate-600">
                                {{ $item->position }}
                            </td>
                            <td class="py-3 px-3 text-right">
                                <form action="{{ route('event.checkin.toggle', ['slug' => $event->slug, 'id' => $item->id]) }}" method="POST">
                                    @csrf
                                    <button type="submit" 
                                            onclick="return confirm('Apakah Anda yakin ingin membatalkan status check-in untuk {{ $item->full_name }}?')"
                                            class="text-rose-600 hover:text-rose-800 font-semibold text-[11px] underline cursor-pointer">
                                        Batalkan
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400 text-xs">
                                Belum ada peserta yang melakukan check-in hari ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function checkinScanner() {
    return {
        isScanning: false,
        processing: false,
        manualToken: '',
        html5QrCode: null,
        lastResult: null,
        stats: {
            totalRegistered: {{ $totalRegistered }},
            totalCheckedIn: {{ $totalCheckedIn }},
            totalPending: {{ $totalPending }}
        },

        init() {
            // Autostart scanner on mount
            this.$nextTick(() => {
                this.startScanner();
            });
        },

        // Web Audio Synthesizer Beep
        playBeep(success = true) {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.connect(gain);
                gain.connect(ctx.destination);

                if (success) {
                    osc.frequency.value = 880; // A5 note (clean high beep)
                    gain.gain.setValueAtTime(0.2, ctx.currentTime);
                    osc.start();
                    osc.stop(ctx.currentTime + 0.15);
                } else {
                    osc.frequency.value = 300; // Low buzz for duplicate / error
                    gain.gain.setValueAtTime(0.3, ctx.currentTime);
                    osc.start();
                    osc.stop(ctx.currentTime + 0.3);
                }
            } catch (e) {
                console.log("Audio not supported or blocked");
            }
        },

        startScanner() {
            if (this.html5QrCode) {
                return;
            }

            if (typeof Html5Qrcode === 'undefined') {
                console.warn('Html5Qrcode is loading...');
                setTimeout(() => this.startScanner(), 500);
                return;
            }

            this.html5QrCode = new Html5Qrcode("qr-reader");
            const config = { fps: 10, qrbox: { width: 250, height: 250 } };

            this.html5QrCode.start(
                { facingMode: "environment" },
                config,
                (decodedText) => {
                    this.onScanSuccess(decodedText);
                },
                (errorMessage) => {
                    // Scanning loop ignore
                }
            ).then(() => {
                this.isScanning = true;
            }).catch(err => {
                console.error("Camera start error:", err);
                this.isScanning = false;
            });
        },

        stopScanner() {
            if (this.html5QrCode && this.isScanning) {
                this.html5QrCode.stop().then(() => {
                    this.html5QrCode.clear();
                    this.html5QrCode = null;
                    this.isScanning = false;
                }).catch(err => console.error(err));
            }
        },

        toggleScanner() {
            if (this.isScanning) {
                this.stopScanner();
            } else {
                this.startScanner();
            }
        },

        onScanSuccess(decodedText) {
            if (this.processing) return;
            this.sendCheckinRequest(decodedText);
        },

        submitManualToken() {
            if (!this.manualToken || this.processing) return;
            this.sendCheckinRequest(this.manualToken);
            this.manualToken = '';
        },

        async sendCheckinRequest(token) {
            this.processing = true;

            try {
                const response = await fetch("{{ route('event.checkin.scan', ['slug' => $event->slug]) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ token: token })
                });

                const data = await response.json();
                this.lastResult = data;

                if (response.ok && data.status === 'checkin_success') {
                    this.playBeep(true);
                    this.stats.totalCheckedIn++;
                    this.stats.totalPending = Math.max(0, this.stats.totalPending - 1);
                } else {
                    this.playBeep(false);
                }
            } catch (err) {
                console.error(err);
                this.lastResult = {
                    success: false,
                    status: 'not_found',
                    message: 'Terjadi kesalahan jaringan atau server saat check-in.'
                };
                this.playBeep(false);
            } finally {
                // Cooldown 2 seconds before next scan to prevent rapid multiple scans
                setTimeout(() => {
                    this.processing = false;
                }, 2000);
            }
        }
    };
}
</script>
@endpush

