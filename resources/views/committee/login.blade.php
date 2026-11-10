@extends('layouts.app', ['title' => 'Login Panitia Check-in - ' . $event->title, 'event' => $event])

@section('content')
<div class="max-w-md mx-auto px-4 py-8">
    <div class="bg-white rounded-3xl p-8 border border-slate-200/90 shadow-xl text-center">
        <!-- Shield Icon -->
        <div class="w-16 h-16 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center mx-auto mb-4 border border-indigo-100 shadow-sm">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
            </svg>
        </div>

        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Panel Check-in Panitia</h1>
        <p class="text-xs text-slate-500 mt-1 mb-6">
            Masukkan password event <span class="font-semibold text-slate-800">"{{ $event->title }}"</span> untuk membuka kamera scanner check-in.
        </p>

        <form action="{{ route('event.checkin.auth', ['slug' => $event->slug]) }}" method="POST" class="space-y-4 text-left">
            @csrf

            <div>
                <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    Password Event
                </label>
                <div class="relative">
                    <input type="password" 
                           name="password" 
                           id="password" 
                           required 
                           autofocus
                           placeholder="Ketik password event..." 
                           class="block w-full px-4 py-3 text-slate-900 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 transition-all">
                </div>
            </div>

            <button type="submit" 
                    class="w-full flex items-center justify-center gap-2 py-3.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm shadow-md shadow-indigo-600/30 transition-all cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                </svg>
                <span>Buka Scanner Kamera</span>
            </button>
        </form>
    </div>
</div>
@endsection

