@extends('layouts.admin', ['title' => 'Tambah Event Baru', 'headerTitle' => 'Buat Event Baru'])

@section('content')
<div class="max-w-3xl">

    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm">
        <div class="mb-6">
            <h2 class="text-lg font-bold text-slate-900">Formulir Pembuatan Event Baru</h2>
            <p class="text-xs text-slate-500 mt-0.5">Tentukan judul, jadwal, slug URL, dan password akses untuk rekap data kehadiran.</p>
        </div>

        <form action="{{ route('admin.events.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Judul Event -->
            <div>
                <label for="title" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    Judul Acara / Event <span class="text-rose-500">*</span>
                </label>
                <input type="text" 
                       name="title" 
                       id="title" 
                       required 
                       value="{{ old('title') }}"
                       placeholder="Contoh: Rapat Kerja Wilayah 2026" 
                       class="block w-full px-4 py-3 text-slate-900 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 transition-all">
                @error('title')
                    <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Custom Slug & Password Akses Rekap Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="slug" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Custom Slug URL <span class="text-slate-400 font-normal">(Opsional)</span>
                    </label>
                    <div class="relative flex items-center">
                        <span class="pl-3.5 pr-1 text-xs text-slate-400 font-mono">/e/</span>
                        <input type="text" 
                               name="slug" 
                               id="slug" 
                               value="{{ old('slug') }}"
                               placeholder="rakerwil-2026" 
                               class="block w-full pr-4 py-3 text-slate-900 bg-slate-50 border border-slate-200 rounded-xl text-sm font-mono focus:bg-white focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 transition-all">
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">Kosongkan jika ingin dibuat otomatis dari judul.</p>
                    @error('slug')
                        <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="access_password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Password Akses Rekap & Panitia <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           name="access_password" 
                           id="access_password" 
                           required 
                           value="{{ old('access_password', 'panitia123') }}"
                           placeholder="Contoh: rakerwil123" 
                           class="block w-full px-4 py-3 text-slate-900 bg-slate-50 border border-slate-200 rounded-xl text-sm font-mono focus:bg-white focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 transition-all">
                    <p class="text-[11px] text-slate-400 mt-1">Password ini digunakan panitia untuk akses scan & rekap data.</p>
                    @error('access_password')
                        <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Deskripsi Event -->
            <div>
                <label for="description" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    Deskripsi Event
                </label>
                <textarea name="description" 
                          id="description" 
                          rows="3" 
                          placeholder="Jelaskan gambaran umum acara atau instruksi tambahan bagi peserta..." 
                          class="block w-full px-4 py-3 text-slate-900 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 transition-all">{{ old('description') }}</textarea>
            </div>

            <!-- Tanggal, Waktu Mulai & Selesai Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div>
                    <label for="event_date" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Tanggal Acara
                    </label>
                    <input type="date" 
                           name="event_date" 
                           id="event_date" 
                           value="{{ old('event_date') }}"
                           class="block w-full px-4 py-3 text-slate-900 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-indigo-600">
                </div>

                <div>
                    <label for="start_time" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Waktu Mulai
                    </label>
                    <input type="time" 
                           name="start_time" 
                           id="start_time" 
                           value="{{ old('start_time', '08:00') }}"
                           class="block w-full px-4 py-3 text-slate-900 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-indigo-600">
                </div>

                <div>
                    <label for="end_time" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Waktu Selesai
                    </label>
                    <input type="time" 
                           name="end_time" 
                           id="end_time" 
                           value="{{ old('end_time', '16:00') }}"
                           class="block w-full px-4 py-3 text-slate-900 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-indigo-600">
                </div>
            </div>

            <!-- Lokasi Acara -->
            <div>
                <label for="location" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    Tempat / Lokasi Acara
                </label>
                <input type="text" 
                       name="location" 
                       id="location" 
                       value="{{ old('location') }}"
                       placeholder="Contoh: Auditorium Utama Gedung Graha Pekanbaru" 
                       class="block w-full px-4 py-3 text-slate-900 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-indigo-600">
            </div>

            <!-- Pengaturan Fitur Tambahan (Checkboxes) -->
            <div class="pt-4 border-t border-slate-100 space-y-3">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 w-4 h-4">
                    <span class="text-xs font-medium text-slate-700">Aktifkan formulir presensi ini (peserta dapat membuka dan mengisi form).</span>
                </label>

                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="allow_duplicate_submission" value="1" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 w-4 h-4">
                    <span class="text-xs font-medium text-slate-700">Izinkan pengisian ganda (default tidak dicentang: mencegah peserta absen dua kali dengan email/WA sama).</span>
                </label>
            </div>

            <!-- Action Buttons -->
            <div class="pt-4 flex items-center gap-3">
                <button type="submit" 
                        class="px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/30 transition-all cursor-pointer">
                    Simpan Event
                </button>
                <a href="{{ route('admin.events.index') }}" class="px-5 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>

</div>
@endsection

