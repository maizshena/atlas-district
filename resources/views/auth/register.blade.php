@extends('layouts.guest', ['title' => 'Daftar akun'])

@section('content')
    <div class="mx-auto max-w-md">
        <div class="rounded-2xl border border-white/10 bg-slate-900 p-6 sm:p-8">
            <h1 class="text-2xl font-semibold">Buat akun</h1>
            <p class="mt-2 text-sm text-slate-400">Daftar lalu verifikasi email untuk membeli tiket resmi.</p>

            <form method="POST" action="{{ route('auth.register') }}" class="mt-6 space-y-4">
                @csrf
                <x-input label="Nama lengkap" name="name" autocomplete="name" required autofocus />
                <x-input label="Email" name="email" type="email" autocomplete="email" required />
                <x-input label="Kata sandi" name="password" type="password" autocomplete="new-password" required />
                <p class="-mt-2 text-xs text-slate-500">Minimal 8 karakter.</p>
                <x-input label="Ulangi kata sandi" name="password_confirmation" type="password" autocomplete="new-password" required />
                <x-button type="submit" class="w-full" data-submit>Daftar dan kirim OTP</x-button>
            </form>

            <p class="mt-6 text-center text-sm text-slate-400">
                Sudah punya akun? <a href="{{ route('login') }}" class="font-medium text-indigo-300 hover:text-indigo-200">Masuk</a>
            </p>
        </div>
    </div>
@endsection
