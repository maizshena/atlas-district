@extends('layouts.guest', ['title' => 'Masuk'])

@section('content')
    <div class="mx-auto w-full max-w-md py-6">
        <x-bento.tile span="1x1" tone="raised" class="!col-span-1 !row-span-1">
            <h1 class="text-title text-white">Masuk</h1>
            <p class="mt-2 text-caption text-gray">Masuk untuk memesan tiket konser resmi.</p>

            <form method="POST" action="{{ route('auth.login') }}" class="mt-6 space-y-4">
                @csrf
                <x-input label="Email" name="email" type="email" autocomplete="email" required autofocus />
                <x-input label="Kata sandi" name="password" type="password" autocomplete="current-password" required />
                <label class="flex min-h-11 cursor-pointer items-center gap-2.5 text-caption text-gray">
                    <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-border-strong bg-surface-2 accent-white">
                    Ingat saya
                </label>
                <x-button type="submit" class="w-full" data-submit>Masuk</x-button>
            </form>

            <p class="mt-6 border-t border-border-token pt-4 text-caption text-gray">
                Belum punya akun? <a href="{{ route('register') }}" class="font-semibold text-white underline underline-offset-4">Daftar</a>
            </p>
            <p class="mt-2 text-caption text-gray">
                Sudah daftar tapi belum verifikasi? <a href="{{ route('auth.verify') }}" class="font-semibold text-white underline underline-offset-4">Masukkan kode OTP</a>
            </p>
        </x-bento.tile>
    </div>
@endsection
