@extends('layouts.app', ['title' => 'Akses Rekap Kehadiran - ' . $event->title, 'event' => $event])

@section('content')
<div class="max-w-md mx-auto px-4 py-8">
    <div class="bg-white rounded-3xl p-8 border border-slate-200/90 shadow-xl text-center">
        <!-- Key/Lock Icon -->
        <div class="w-16 h-16 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center mx-auto mb-4 border border-indigo-100 shadow-sm">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
        </div>

        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Rekapitulasi Kehadiran</h1>
        <p class="text-xs text-slate-500 mt-1 mb-6">
            Data kehadiran event <span class="font-semibold text-slate-800">"{{ $event->title }}"</span> terproteksi. Silakan masukkan password event untuk melihat & mengunduh laporan.
        </p>

        <form action="{{ route('event.report.auth', ['slug' => $event->slug]) }}" method="POST" class="space-y-4 text-left">
            @csrf

            <div>
                <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    Password Event
                </label>
                <input type="password" 
                       name="password" 
                       id="password" 
                       required 
                       autofocus
                       placeholder="Masukkan password event..." 
                       class="block w-full px-4 py-3 text-slate-900 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 transition-all">
            </div>

            <button type="submit" 
                    class="w-full flex items-center justify-center gap-2 py-3.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm shadow-md shadow-indigo-600/30 transition-all cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" />
                </svg>
                <span>Buka Rekap Data</span>
            </button>
        </form>
    </div>
</div>
@endsection

