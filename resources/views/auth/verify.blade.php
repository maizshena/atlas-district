@extends('layouts.guest', ['title' => 'Verifikasi akun'])

@section('content')
    <div class="mx-auto w-full max-w-md py-6">
        <x-bento.tile span="1x1" tone="raised" class="!col-span-1 !row-span-1">
            <h1 class="text-title text-white">Verifikasi email</h1>
            <p class="mt-2 text-caption text-gray">Masukkan enam digit kode yang kami kirim ke email Anda.</p>

            <form method="POST" action="{{ route('auth.verify.submit') }}" class="mt-6 space-y-4">
                @csrf
                <x-input label="Email" name="email" type="email" :value="$email" autocomplete="email" required />
                <x-input label="Kode OTP" name="code" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" autocomplete="one-time-code" class="num text-center tracking-[0.5em]" required autofocus />
                <x-button type="submit" class="w-full" data-submit>Verifikasi</x-button>
            </form>

            <form method="POST" action="{{ route('auth.resend') }}" class="mt-3" x-data="{ sent: false }" @submit="setTimeout(() => sent = true, 50)">
                @csrf
                <input type="hidden" name="email" value="{{ old('email', $email) }}">
                <button class="flex min-h-11 w-full items-center justify-center rounded-btn border border-border-token px-4 text-caption font-semibold text-white transition bento-hover:border-border-strong bento-hover:bg-white/5" :disabled="sent" data-submit>
                    <span x-show="!sent">Kirim ulang kode</span>
                    <span x-cloak x-show="sent">Kode dikirim ulang</span>
                </button>
            </form>

            <p class="mt-6 border-t border-border-token pt-4 text-caption text-gray">
                Sudah terverifikasi? <a href="{{ route('login') }}" class="font-semibold text-white underline underline-offset-4">Masuk</a>
            </p>
        </x-bento.tile>
    </div>
@endsection
