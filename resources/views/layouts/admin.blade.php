<!doctype html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#000000">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title.' · ' : '' }}Admin Concert District</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-surface-0 text-white antialiased" x-data="{ open: false }">
    <a href="#konten" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:rounded-btn focus:bg-white focus:px-4 focus:py-2.5 focus:text-caption focus:font-semibold focus:text-black">Lewati ke konten</a>

    <div class="mx-auto flex min-h-screen w-full max-w-7xl flex-col lg:flex-row">
        <aside class="border-b border-border-token bg-surface-1 lg:sticky lg:top-0 lg:h-screen lg:w-64 lg:shrink-0 lg:overflow-y-auto lg:border-b-0 lg:border-r">
            <div class="flex items-center justify-between px-4 py-3.5 lg:px-5">
        <a href="{{ route(auth()->user()->homeRouteName()) }}" class="flex min-h-11 items-center gap-2.5 text-heading font-bold">
                    <span class="grid h-9 w-9 place-items-center rounded-btn bg-white text-caption font-extrabold text-black">CD</span>
                    {{ auth()->user()->isAdmin() ? 'Admin' : 'Organizer' }}
                </a>
                <button type="button" class="flex min-h-11 items-center rounded-btn border border-border-token px-3 text-caption text-white lg:hidden" @click="open = !open" :aria-expanded="open ? 'true' : 'false'" aria-controls="admin-nav" aria-label="Buka menu admin">Menu</button>
            </div>
        <nav id="admin-nav" class="space-y-1 px-3 pb-5" :class="open ? 'block' : 'hidden lg:block'" aria-label="Navigasi admin">
            @if (auth()->user()->isAdmin())
                <x-admin-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">Dashboard</x-admin-nav-link>
            @endif
            <x-admin-nav-link :href="route('admin.events.index')" :active="request()->routeIs('admin.events.*')">Event</x-admin-nav-link>
            @if (auth()->user()->isAdmin())
                <x-admin-nav-link :href="route('admin.venues.index')" :active="request()->routeIs('admin.venues.*')">Venue</x-admin-nav-link>
                <x-admin-nav-link :href="route('admin.artists.index')" :active="request()->routeIs('admin.artists.*')">Artis</x-admin-nav-link>
                <x-admin-nav-link :href="route('admin.orders.index')" :active="request()->routeIs('admin.orders.*')">Pesanan</x-admin-nav-link>
                <x-admin-nav-link :href="route('admin.gate')" :active="request()->routeIs('admin.gate')">Validasi gate</x-admin-nav-link>
            @endif
            <div class="!mt-4 space-y-1 border-t border-border-token pt-3">
                <p class="px-3 pb-1 text-micro font-semibold uppercase tracking-wider text-gray">Tambah cepat</p>
                <x-admin.quick-link :href="route('admin.events.create')">Event baru</x-admin.quick-link>
                @if (auth()->user()->isAdmin())
                    <x-admin.quick-link :href="route('admin.venues.create')">Venue baru</x-admin.quick-link>
                    <x-admin.quick-link :href="route('admin.artists.create')">Artis baru</x-admin.quick-link>
                @endif
            </div>
            <div class="space-y-1 border-t border-border-token pt-3">
                <x-admin.quick-link :href="route('events.index')">Lihat situs publik</x-admin.quick-link>
                <form method="POST" action="{{ route('auth.logout') }}">
                    @csrf
                    <x-admin.quick-link data-submit>Keluar</x-admin.quick-link>
                </form>
            </div>
        </nav>
        </aside>

        <main id="konten" class="min-w-0 flex-1 px-4 py-6 sm:px-8 sm:py-10">
            <x-alert />
            @yield('content')
        </main>
    </div>
</body>
</html>
