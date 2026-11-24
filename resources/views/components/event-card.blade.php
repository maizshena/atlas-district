<a href="{{ route('events.show', $event->slug) }}" class="group bento-reveal flex h-full flex-col overflow-hidden rounded-tile border border-border-token bg-surface-1 transition duration-150 tile-focus bento-hover:border-border-strong active:scale-[0.98]">
    <div class="relative aspect-[16/10] overflow-hidden bg-surface-3">
        @if ($event->banner_url)
            <img src="{{ $event->banner_url }}" alt="Banner {{ $event->name }}" loading="lazy" decoding="async" class="h-full w-full object-cover">
        @else
            <div class="grid h-full place-items-center bg-linear-to-br from-surface-3 to-surface-2 text-title font-extrabold text-gray">CD</div>
        @endif
        <div class="absolute inset-x-0 bottom-0 h-2/3 bg-linear-to-t from-scrim to-transparent" aria-hidden="true"></div>
        <p class="absolute bottom-3 left-3 rounded-full bg-white px-2.5 py-1 text-micro font-bold text-black">{{ $event->start_date->timezone('Asia/Jakarta')->translatedFormat('d M · H:i') }} WIB</p>
    </div>
    <div class="flex flex-1 flex-col p-4">
        <h3 class="line-clamp-2 text-heading font-semibold leading-snug text-white">{{ $event->name }}</h3>
        <p class="mt-1 flex items-center gap-1.5 truncate text-caption text-gray">
            <x-icon name="pin" :size="14" />
            {{ $event->venue->name }} &middot; {{ $event->venue->city }}
        </p>
        @php($startingPrice = $event->ticketCategories->min('price'))
        <p class="mt-auto flex items-baseline justify-between gap-2 pt-4 text-micro text-gray">
            <span>Mulai</span>
            <span class="num text-caption text-white">{{ $startingPrice === null ? 'Belum tersedia' : \App\Support\Formatters::rupiah($startingPrice) }}</span>
        </p>
    </div>
</a>
