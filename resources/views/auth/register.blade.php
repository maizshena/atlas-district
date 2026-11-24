@extends('layouts.guest', ['title' => 'Daftar akun'])

@section('content')
    <div class="mx-auto w-full max-w-md py-6">
        <x-bento.tile span="1x1" tone="raised" class="!col-span-1 !row-span-1">
            <h1 class="text-title text-white">Buat akun</h1>
            <p class="mt-2 text-caption text-gray">Daftar lalu verifikasi email untuk membeli tiket resmi.</p>

            <form method="POST" action="{{ route('auth.register') }}" class="mt-6 space-y-4">
                @csrf
                <x-input label="Nama lengkap" name="name" autocomplete="name" required autofocus />
                <x-input label="Email" name="email" type="email" autocomplete="email" required />
                <x-input label="Kata sandi" name="password" type="password" autocomplete="new-password" hint="Minimal 8 karakter." required />
                <x-input label="Ulangi kata sandi" name="password_confirmation" type="password" autocomplete="new-password" required />
                <x-button type="submit" class="w-full" data-submit>Daftar dan kirim OTP</x-button>
            </form>

            <p class="mt-6 border-t border-border-token pt-4 text-caption text-gray">
                Sudah punya akun? <a href="{{ route('login') }}" class="font-semibold text-white underline underline-offset-4">Masuk</a>
            </p>
        </x-bento.tile>
    </div>
@endsection
