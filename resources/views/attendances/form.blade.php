@extends('layouts.app', ['title' => 'Formulir Kehadiran - ' . $event->title, 'event' => $event])

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6">

    <!-- Event Header Card -->
    <div class="bg-gradient-to-br from-indigo-700 via-indigo-800 to-slate-900 rounded-3xl p-6 sm:p-10 text-white shadow-xl relative overflow-hidden mb-8">
        <!-- Decorative Ambient Light -->
        <div class="absolute -top-24 -right-24 w-72 h-72 bg-indigo-500/30 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-72 h-72 bg-violet-600/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/30 border border-indigo-400/30 text-indigo-200 text-xs font-semibold uppercase tracking-wider mb-4">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                Formulir Kehadiran Resmi
            </div>

            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-white mb-3">
                {{ $event->title }}
            </h1>

            @if($event->description)
                <p class="text-indigo-100/90 text-sm sm:text-base leading-relaxed mb-6 max-w-2xl">
                    {{ $event->description }}
                </p>
            @endif

            <!-- Event Meta Info -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-4 border-t border-indigo-500/30 text-xs sm:text-sm text-indigo-100">
                @if($event->event_date)
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-indigo-600/50 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <span class="block text-[11px] text-indigo-300 font-medium">Tanggal Acara</span>
                            <span class="font-semibold text-white">{{ $event->event_date->format('d F Y') }}</span>
                        </div>
                    </div>
                @endif

                @if($event->start_time)
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-indigo-600/50 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <span class="block text-[11px] text-indigo-300 font-medium">Waktu Pelaksanaan</span>
                            <span class="font-semibold text-white">{{ substr($event->start_time, 0, 5) }} {{ $event->end_time ? '- ' . substr($event->end_time, 0, 5) : '' }} WIB</span>
                        </div>
                    </div>
                @endif

                @if($event->location)
                    <div class="flex items-center gap-2.5 sm:col-span-2">
                        <div class="w-8 h-8 rounded-lg bg-indigo-600/50 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div>
                            <span class="block text-[11px] text-indigo-300 font-medium">Tempat / Lokasi</span>
                            <span class="font-semibold text-white">{{ $event->location }}</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Attendance Form Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200/80 shadow-sm" x-data="{ submitting: false }">
        <div class="mb-8">
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Konfirmasi Kehadiran Peserta</h2>
            <p class="text-sm text-slate-500 mt-1">Silakan lengkapi data diri Anda di bawah ini untuk mendapatkan QR Code e-tiket kehadiran resmi.</p>
        </div>

        <form action="{{ route('attendance.store', ['slug' => $event->slug]) }}" method="POST" @submit="submitting = true" class="space-y-6">
            @csrf

            <!-- Nama Lengkap -->
            <div>
                <label for="full_name" class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Nama Lengkap <span class="text-rose-500">*</span>
                </label>
                <div class="relative rounded-xl shadow-xs">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <input type="text" 
                           name="full_name" 
                           id="full_name" 
                           required 
                           value="{{ old('full_name') }}"
                           placeholder="Masukkan nama lengkap beserta gelar jika ada" 
                           class="block w-full pl-11 pr-4 py-3 text-slate-900 bg-slate-50/50 border rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 transition-all {{ $errors->has('full_name') ? 'border-rose-400' : 'border-slate-200' }}">
                </div>
                @error('full_name')
                    <p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email & WhatsApp Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Alamat Email <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative rounded-xl shadow-xs">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <input type="email" 
                               name="email" 
                               id="email" 
                               required 
                               value="{{ old('email') }}"
                               placeholder="nama@email.com" 
                               class="block w-full pl-11 pr-4 py-3 text-slate-900 bg-slate-50/50 border rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 transition-all {{ $errors->has('email') ? 'border-rose-400' : 'border-slate-200' }}">
                    </div>
                    <p class="mt-1 text-[11px] text-slate-400">E-tiket QR Code akan dikirimkan ke email ini.</p>
                    @error('email')
                        <p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nomor WhatsApp -->
                <div>
                    <label for="phone_number" class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Nomor WhatsApp <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative rounded-xl shadow-xs">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                        <input type="tel" 
                               name="phone_number" 
                               id="phone_number" 
                               required 
                               value="{{ old('phone_number') }}"
                               placeholder="081234567890" 
                               class="block w-full pl-11 pr-4 py-3 text-slate-900 bg-slate-50/50 border rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 transition-all {{ $errors->has('phone_number') ? 'border-rose-400' : 'border-slate-200' }}">
                    </div>
                    @error('phone_number')
                        <p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Instansi (Wajihah) & Jabatan (Amanah) Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <!-- Instansi (Wajihah) -->
                <div>
                    <label for="institution" class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Instansi / Organisasi (Wajihah) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative rounded-xl shadow-xs">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <input type="text" 
                               name="institution" 
                               id="institution" 
                               required 
                               value="{{ old('institution') }}"
                               placeholder="Nama instansi / wajihah" 
                               class="block w-full pl-11 pr-4 py-3 text-slate-900 bg-slate-50/50 border rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 transition-all {{ $errors->has('institution') ? 'border-rose-400' : 'border-slate-200' }}">
                    </div>
                    @error('institution')
                        <p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jabatan (Amanah) -->
                <div>
                    <label for="position" class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Jabatan / Peran (Amanah) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative rounded-xl shadow-xs">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <input type="text" 
                               name="position" 
                               id="position" 
                               required 
                               value="{{ old('position') }}"
                               placeholder="Jabatan atau amanah di instansi" 
                               class="block w-full pl-11 pr-4 py-3 text-slate-900 bg-slate-50/50 border rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 transition-all {{ $errors->has('position') ? 'border-rose-400' : 'border-slate-200' }}">
                    </div>
                    @error('position')
                        <p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Konfirmasi Kehadiran Dropdown -->
            <div>
                <label for="attendance_status" class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Konfirmasi Kehadiran <span class="text-rose-500">*</span>
                </label>
                <div class="relative rounded-xl shadow-xs">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <select name="attendance_status" 
                            id="attendance_status" 
                            required 
                            class="block w-full pl-11 pr-10 py-3 text-slate-900 bg-slate-50/50 border rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 transition-all appearance-none {{ $errors->has('attendance_status') ? 'border-rose-400' : 'border-slate-200' }}">
                        <option value="hadir" {{ old('attendance_status', 'hadir') === 'hadir' ? 'selected' : '' }}>
                            ✅ Hadir Tepat Waktu
                        </option>
                        <option value="hadir_terlambat" {{ old('attendance_status') === 'hadir_terlambat' ? 'selected' : '' }}>
                            ⏳ Hadir Terlambat (Menyusul)
                        </option>
                        <option value="tidak_hadir" {{ old('attendance_status') === 'tidak_hadir' ? 'selected' : '' }}>
                            ❌ Tidak Hadir (Izin / Berhalangan)
                        </option>
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
                @error('attendance_status')
                    <p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Catatan Tambahan (Opsional) -->
            <div>
                <label for="notes" class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Catatan Tambahan <span class="text-xs font-normal text-slate-400">(Opsional)</span>
                </label>
                <textarea name="notes" 
                          id="notes" 
                          rows="3" 
                          placeholder="Tuliskan catatan khusus atau alasan jika izin/terlambat..." 
                          class="block w-full px-4 py-3 text-slate-900 bg-slate-50/50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 transition-all">{{ old('notes') }}</textarea>
            </div>

            <!-- Submit Button -->
            <div class="pt-4">
                <button type="submit" 
                        :disabled="submitting" 
                        class="w-full flex items-center justify-center gap-3 py-4 px-6 rounded-2xl bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-bold text-base shadow-lg shadow-indigo-600/30 hover:shadow-indigo-600/40 active:scale-[0.99] transition-all disabled:opacity-75 disabled:cursor-not-allowed cursor-pointer">
                    <span x-show="!submitting" class="flex items-center gap-2">
                        <span>Kirim & Dapatkan QR Code Check-in</span>
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </span>
                    <span x-show="submitting" style="display: none;" class="flex items-center gap-2">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Menyimpan & Membuat Tiket QR...</span>
                    </span>
                </button>
                <p class="text-center text-xs text-slate-400 mt-3">
                    Data Anda dilindungi dan hanya digunakan untuk keperluan rekap kehadiran kegiatan ini.
                </p>
            </div>
        </form>
    </div>
</div>
@endsection

