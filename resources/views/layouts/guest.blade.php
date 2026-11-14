<!doctype html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Concert District' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-full flex-col bg-slate-950 text-slate-100 antialiased" x-data>
    <header class="border-b border-white/10 bg-slate-950/95">
        <nav class="mx-auto flex w-full max-w-6xl items-center justify-between px-4 py-4 sm:px-6">
            <a href="{{ route('events.index') }}" class="flex items-center gap-2 text-lg font-semibold tracking-tight">
                <span class="grid h-8 w-8 place-items-center rounded-lg bg-indigo-500 text-sm font-bold">CD</span>
                Concert District
            </a>
            <div class="flex items-center gap-2 text-sm">
                @auth
                    <a href="{{ route('orders.index') }}" class="hidden rounded-lg px-3 py-2 text-slate-300 hover:bg-white/5 hover:text-white sm:inline-block">Pesanan</a>
                    <a href="{{ route('tickets.index') }}" class="hidden rounded-lg px-3 py-2 text-slate-300 hover:bg-white/5 hover:text-white sm:inline-block">Tiket</a>
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="rounded-lg px-3 py-2 text-slate-300 hover:bg-white/5 hover:text-white">Admin</a>
                    @endif
                    <form method="POST" action="{{ route('auth.logout') }}">
                        @csrf
                        <button class="rounded-lg border border-white/15 px-3 py-2 font-medium text-slate-200 hover:bg-white/10" data-submit>Keluar</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="rounded-lg px-3 py-2 text-slate-300 hover:bg-white/5 hover:text-white">Masuk</a>
                    <a href="{{ route('register') }}" class="rounded-lg bg-indigo-500 px-3 py-2 font-medium hover:bg-indigo-400">Daftar</a>
                @endauth
            </div>
        </nav>
        @auth
            <nav class="mx-auto flex w-full max-w-6xl gap-1 border-t border-white/10 px-4 py-2 text-sm sm:hidden sm:px-6">
                <a href="{{ route('orders.index') }}" class="flex-1 rounded-lg px-3 py-2 text-center text-slate-300 hover:bg-white/5">Pesanan</a>
                <a href="{{ route('tickets.index') }}" class="flex-1 rounded-lg px-3 py-2 text-center text-slate-300 hover:bg-white/5">Tiket</a>
            </nav>
        @endauth
    </header>

    <main class="mx-auto w-full max-w-6xl flex-1 px-4 py-8 sm:px-6 sm:py-10">
        <x-alert />
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    <footer class="border-t border-white/10 px-4 py-6 text-center text-xs text-slate-500 sm:px-6">
        Concert District &middot; Reservasi tiket resmi &middot; {{ now()->timezone('Asia/Jakarta')->translatedFormat('d F Y') }}
    </footer>
</body>
</html>
