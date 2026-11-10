@extends('layouts.admin', ['title' => 'Edit Event - ' . $event->title, 'headerTitle' => 'Edit Event'])

@section('content')
<div class="max-w-3xl">

    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Perbarui Pengaturan Event</h2>
                <p class="text-xs text-slate-500 mt-0.5">Ubah informasi acara, jadwal, status aktif, atau ganti password akses.</p>
            </div>
            <a href="{{ route('admin.events.attendances.index', ['event' => $event->id]) }}" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold">
                Lihat Peserta ({{ $event->attendances()->count() }})
            </a>
        </div>

        <form action="{{ route('admin.events.update', ['event' => $event->id]) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Judul Event -->
            <div>
                <label for="title" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    Judul Acara / Event <span class="text-rose-500">*</span>
                </label>
                <input type="text" 
                       name="title" 
                       id="title" 
                       required 
                       value="{{ old('title', $event->title) }}"
                       class="block w-full px-4 py-3 text-slate-900 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 transition-all">
                @error('title')
                    <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Slug & Password Akses Rekap Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="slug" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Slug URL <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative flex items-center">
                        <span class="pl-3.5 pr-1 text-xs text-slate-400 font-mono">/e/</span>
                        <input type="text" 
                               name="slug" 
                               id="slug" 
                               required
                               value="{{ old('slug', $event->slug) }}"
                               class="block w-full pr-4 py-3 text-slate-900 bg-slate-50 border border-slate-200 rounded-xl text-sm font-mono focus:bg-white focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 transition-all">
                    </div>
                    @error('slug')
                        <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="access_password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Ganti Password Akses Event
                    </label>
                    <input type="text" 
                           name="access_password" 
                           id="access_password" 
                           placeholder="Kosongkan jika tidak ingin mengubah password" 
                           class="block w-full px-4 py-3 text-slate-900 bg-slate-50 border border-slate-200 rounded-xl text-sm font-mono focus:bg-white focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 transition-all">
                    <p class="text-[11px] text-slate-400 mt-1">Isi hanya jika ingin mengganti password akses panitia saat ini.</p>
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
                          class="block w-full px-4 py-3 text-slate-900 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 transition-all">{{ old('description', $event->description) }}</textarea>
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
                           value="{{ old('event_date', $event->event_date ? $event->event_date->format('Y-m-d') : '') }}"
                           class="block w-full px-4 py-3 text-slate-900 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-indigo-600">
                </div>

                <div>
                    <label for="start_time" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Waktu Mulai
                    </label>
                    <input type="time" 
                           name="start_time" 
                           id="start_time" 
                           value="{{ old('start_time', $event->start_time ? substr($event->start_time, 0, 5) : '') }}"
                           class="block w-full px-4 py-3 text-slate-900 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-indigo-600">
                </div>

                <div>
                    <label for="end_time" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Waktu Selesai
                    </label>
                    <input type="time" 
                           name="end_time" 
                           id="end_time" 
                           value="{{ old('end_time', $event->end_time ? substr($event->end_time, 0, 5) : '') }}"
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
                       value="{{ old('location', $event->location) }}"
                       class="block w-full px-4 py-3 text-slate-900 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-indigo-600">
            </div>

            <!-- Pengaturan Fitur Tambahan (Checkboxes) -->
            <div class="pt-4 border-t border-slate-100 space-y-3">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $event->is_active) ? 'checked' : '' }} class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 w-4 h-4">
                    <span class="text-xs font-medium text-slate-700">Aktifkan formulir presensi ini.</span>
                </label>

                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="allow_duplicate_submission" value="1" {{ old('allow_duplicate_submission', $event->allow_duplicate_submission) ? 'checked' : '' }} class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 w-4 h-4">
                    <span class="text-xs font-medium text-slate-700">Izinkan pengisian ganda (default dinonaktifkan untuk mencegah duplikasi absen).</span>
                </label>
            </div>

            <!-- Action Buttons -->
            <div class="pt-4 flex items-center gap-3">
                <button type="submit" 
                        class="px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/30 transition-all cursor-pointer">
                    Simpan Perubahan
                </button>
                <a href="{{ route('admin.events.index') }}" class="px-5 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors">
                    Kembali
                </a>
            </div>
        </form>
    </div>

</div>
@endsection

