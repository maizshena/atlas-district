<!doctype html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#000000">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Concert District' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-full flex-col bg-surface-0 text-white antialiased" x-data="{ open: false }">
    <a href="#konten" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:rounded-btn focus:bg-white focus:px-4 focus:py-2.5 focus:text-caption focus:font-semibold focus:text-black">Lewati ke konten</a>

    <header class="sticky top-0 z-40 border-b border-border-token bg-surface-0/90 backdrop-blur">
        <nav class="mx-auto flex w-full max-w-7xl items-center justify-between gap-3 px-4 py-3 sm:px-6 lg:px-8" aria-label="Navigasi utama">
            <a href="{{ route('events.index') }}" class="flex min-h-11 items-center gap-2.5 rounded-btn text-heading font-bold tracking-tight">
                <span class="grid h-9 w-9 place-items-center rounded-btn bg-white text-caption font-extrabold text-black">CD</span>
                <span class="hidden sm:inline">Concert District</span>
            </a>

            <div class="flex items-center gap-1.5 text-caption">
                @auth
                    <a href="{{ route('orders.index') }}" class="hidden min-h-11 items-center rounded-btn px-3 text-gray hover:text-white sm:flex">Pesanan</a>
                    <a href="{{ route('tickets.index') }}" class="hidden min-h-11 items-center rounded-btn px-3 text-gray hover:text-white sm:flex">Tiket</a>
                @if (auth()->user()->canManageEvents())
                    <a href="{{ route(auth()->user()->homeRouteName()) }}" class="flex min-h-11 items-center rounded-btn px-3 text-gray hover:text-white">{{ auth()->user()->isAdmin() ? 'Admin' : 'Kelola event' }}</a>
                @endif
                    <form method="POST" action="{{ route('auth.logout') }}">
                        @csrf
                        <button class="flex min-h-11 items-center rounded-btn border border-border-token px-3.5 font-semibold text-white transition bento-hover:border-border-strong bento-hover:bg-white/5" data-submit>Keluar</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="flex min-h-11 items-center rounded-btn px-3 text-gray hover:text-white">Masuk</a>
                    <a href="{{ route('register') }}" class="flex min-h-11 items-center rounded-btn bg-white px-4 font-semibold text-black transition bento-hover:bg-white/90 active:scale-[0.98]">Daftar</a>
                @endauth
                @auth
                    <button type="button" class="grid h-11 w-11 place-items-center rounded-btn border border-border-token text-white sm:hidden" @click="open = !open" :aria-expanded="open ? 'true' : 'false'" aria-controls="nav-mobile" aria-label="Menu akun">
                        <x-icon name="menu" :size="18" />
                    </button>
                @endauth
            </div>
        </nav>
        @auth
            <nav id="nav-mobile" class="mx-auto flex w-full max-w-7xl gap-1.5 border-t border-border-token px-4 pb-3 pt-2 text-caption sm:hidden" x-show="open" x-cloak aria-label="Navigasi akun">
                <a href="{{ route('orders.index') }}" class="flex min-h-11 flex-1 items-center justify-center rounded-btn border border-border-token text-center text-white">Pesanan</a>
                <a href="{{ route('tickets.index') }}" class="flex min-h-11 flex-1 items-center justify-center rounded-btn border border-border-token text-center text-white">Tiket</a>
            </nav>
        @endauth
    </header>

    <main id="konten" class="mx-auto w-full max-w-7xl flex-1 px-4 py-8 sm:px-6 sm:py-10 lg:px-8">
        <x-alert />
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    <footer class="border-t border-border-token px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto flex w-full max-w-7xl flex-col gap-3 text-caption text-gray sm:flex-row sm:items-center sm:justify-between">
            <p>Concert District &middot; Reservasi tiket resmi konser.</p>
            <p class="num text-micro">{{ now()->timezone('Asia/Jakarta')->translatedFormat('d F Y') }} WIB</p>
        </div>
    </footer>
</body>
</html>
