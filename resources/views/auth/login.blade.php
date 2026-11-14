@extends('layouts.guest', ['title' => 'Masuk'])

@section('content')
    <div class="mx-auto max-w-md">
        <div class="rounded-2xl border border-white/10 bg-slate-900 p-6 sm:p-8">
            <h1 class="text-2xl font-semibold">Masuk</h1>
            <p class="mt-2 text-sm text-slate-400">Masuk untuk memesan tiket konser.</p>

            <form method="POST" action="{{ route('auth.login') }}" class="mt-6 space-y-4">
                @csrf
                <x-input label="Email" name="email" type="email" autocomplete="email" required autofocus />
                <x-input label="Kata sandi" name="password" type="password" autocomplete="current-password" required />
                <label class="flex items-center gap-2 text-sm text-slate-300">
                    <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-white/20 bg-slate-950 text-indigo-500">
                    Ingat saya
                </label>
                <x-button type="submit" class="w-full" data-submit>Masuk</x-button>
            </form>

            <p class="mt-6 text-center text-sm text-slate-400">
                Belum punya akun? <a href="{{ route('register') }}" class="font-medium text-indigo-300 hover:text-indigo-200">Daftar</a>
            </p>
            <p class="mt-2 text-center text-sm text-slate-400">
                Sudah daftar tapi belum verifikasi? <a href="{{ route('auth.verify') }}" class="font-medium text-indigo-300 hover:text-indigo-200">Masukkan kode OTP</a>
            </p>
        </div>
    </div>
@endsection
