@extends('layouts.guest', ['title' => 'Verifikasi akun'])

@section('content')
    <div class="mx-auto max-w-md">
        <div class="rounded-2xl border border-white/10 bg-slate-900 p-6 sm:p-8">
            <h1 class="text-2xl font-semibold">Verifikasi email</h1>
            <p class="mt-2 text-sm text-slate-400">Masukkan enam digit kode yang kami kirim ke email Anda.</p>

            <form method="POST" action="{{ route('auth.verify.submit') }}" class="mt-6 space-y-4">
                @csrf
                <x-input label="Email" name="email" type="email" :value="$email" autocomplete="email" required />
                <x-input label="Kode OTP" name="code" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" autocomplete="one-time-code" class="text-center text-xl tracking-[0.5em]" required autofocus />
                <x-button type="submit" class="w-full" data-submit>Verifikasi</x-button>
            </form>

            <form method="POST" action="{{ route('auth.resend') }}" class="mt-3" x-data="{ sent: false }" @submit="setTimeout(() => sent = true, 50)">
                @csrf
                <input type="hidden" name="email" value="{{ old('email', $email) }}">
                <button class="w-full rounded-xl border border-white/15 px-4 py-2.5 text-sm font-semibold text-slate-200 transition hover:bg-white/10" :disabled="sent" data-submit>
                    <span x-show="!sent">Kirim ulang kode</span>
                    <span x-cloak x-show="sent">Kode dikirim ulang</span>
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-slate-400">
                Sudah terverifikasi? <a href="{{ route('login') }}" class="font-medium text-indigo-300 hover:text-indigo-200">Masuk</a>
            </p>
        </div>
    </div>
@endsection
