{{-- resources/views/layouts/guest.blade.php layout publik dengan komponen kepala dan navigasi --}}
<!doctype html>
<html lang="id" class="h-full">
<head>
    <x-layout.head :title="$title ?? 'Concert District'" />
</head>
<body class="flex min-h-full flex-col bg-surface-0 text-white antialiased" x-data="{ open: false }">
    @php
        $accountLinks = [['orders.index', 'Pesanan'], ['tickets.index', 'Tiket']];
    @endphp

    <x-layout.skip-link />

    <header class="sticky top-0 z-40 border-b border-border-token bg-surface-0/90 backdrop-blur">
        <nav class="mx-auto flex w-full max-w-7xl items-center justify-between gap-3 px-4 py-3 sm:px-6 lg:px-8" aria-label="Navigasi utama">
            <a href="{{ route('events.index') }}" class="flex min-h-11 items-center gap-2.5 rounded-btn text-heading font-bold tracking-tight">
                <span class="grid h-9 w-9 place-items-center rounded-btn bg-white text-caption font-extrabold text-black">CD</span>
                <span class="hidden sm:inline">Concert District</span>
            </a>

            <div class="flex items-center gap-1.5 text-caption">
                @auth
                    @foreach ($accountLinks as [$routeName, $label])
                        <x-layout.nav-link :href="route($routeName)" class="hidden sm:flex">{{ $label }}</x-layout.nav-link>
                    @endforeach
                    @if (auth()->user()->canManageEvents())
                        <x-layout.nav-link :href="route(auth()->user()->homeRouteName())" class="flex">{{ auth()->user()->isAdmin() ? 'Admin' : 'Kelola event' }}</x-layout.nav-link>
                    @endif
                    <form method="POST" action="{{ route('auth.logout') }}">
                        @csrf
                        <x-button type="submit" variant="secondary" class="px-3.5" data-submit>Keluar</x-button>
                    </form>
                    <button type="button" class="grid h-11 w-11 place-items-center rounded-btn border border-border-token text-white sm:hidden" @click="open = !open" :aria-expanded="open ? 'true' : 'false'" aria-controls="nav-mobile" aria-label="Menu akun">
                        <x-icon name="menu" :size="18" />
                    </button>
                @else
                    <x-layout.nav-link :href="route('login')" class="flex">Masuk</x-layout.nav-link>
                    <x-button :href="route('register')">Daftar</x-button>
                @endauth
            </div>
        </nav>
        @auth
            <nav id="nav-mobile" class="mx-auto flex w-full max-w-7xl gap-1.5 border-t border-border-token px-4 pb-3 pt-2 text-caption sm:hidden" x-show="open" x-cloak aria-label="Navigasi akun">
                @foreach ($accountLinks as [$routeName, $label])
                    <x-button :href="route($routeName)" variant="secondary" class="flex-1">{{ $label }}</x-button>
                @endforeach
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