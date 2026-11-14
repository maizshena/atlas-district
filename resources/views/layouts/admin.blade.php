<!doctype html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title.' · ' : '' }}Admin Concert District</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-slate-950 text-slate-100 antialiased" x-data="{ open: false }">
    <div class="mx-auto flex min-h-screen w-full max-w-7xl flex-col lg:flex-row">
        <aside class="border-b border-white/10 bg-[#0d1424] lg:w-64 lg:shrink-0 lg:border-b-0 lg:border-r">
            <div class="flex items-center justify-between px-4 py-4 lg:px-5">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 font-semibold">
                    <span class="grid h-8 w-8 place-items-center rounded-lg bg-indigo-500 text-sm font-bold">CD</span>
                    Admin
                </a>
                <button type="button" class="rounded-lg border border-white/15 px-3 py-1.5 text-sm lg:hidden" @click="open = !open">Menu</button>
            </div>
            <nav class="space-y-1 px-3 pb-4 lg:block" :class="open ? 'block' : 'hidden'" x-cloak>
                <x-admin-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">Dashboard</x-admin-nav-link>
                <x-admin-nav-link :href="route('admin.events.index')" :active="request()->routeIs('admin.events.*')">Event</x-admin-nav-link>
                <x-admin-nav-link :href="route('admin.venues.index')" :active="request()->routeIs('admin.venues.*')">Venue</x-admin-nav-link>
                <x-admin-nav-link :href="route('admin.artists.index')" :active="request()->routeIs('admin.artists.*')">Artis</x-admin-nav-link>
                <x-admin-nav-link :href="route('admin.orders.index')" :active="request()->routeIs('admin.orders.*')">Pesanan</x-admin-nav-link>
                <x-admin-nav-link :href="route('admin.gate')" :active="request()->routeIs('admin.gate')">Validasi gate</x-admin-nav-link>
                <div class="!mt-4 border-t border-white/10 pt-3">
                    <a href="{{ route('events.index') }}" class="block rounded-lg px-3 py-2 text-sm text-slate-400 hover:bg-white/5 hover:text-white">Lihat situs publik</a>
                    <form method="POST" action="{{ route('auth.logout') }}">
                        @csrf
                        <button class="w-full rounded-lg px-3 py-2 text-left text-sm text-slate-400 hover:bg-white/5 hover:text-white" data-submit>Keluar</button>
                    </form>
                </div>
            </nav>
        </aside>

        <main class="flex-1 px-4 py-6 sm:px-8 sm:py-10">
            <x-alert />
            @yield('content')
        </main>
    </div>
</body>
</html>
