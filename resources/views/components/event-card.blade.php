@props(['event'])
<a href="{{ route('events.show', $event->slug) }}" class="group flex h-full flex-col overflow-hidden rounded-2xl border border-white/10 bg-slate-900 transition hover:-translate-y-0.5 hover:border-indigo-400/50 hover:shadow-lg hover:shadow-indigo-950/40">
    <div class="aspect-[16/10] overflow-hidden bg-slate-800">
        @if ($event->banner_url)
            <img src="{{ $event->banner_url }}" alt="Banner {{ $event->name }}" loading="lazy" class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
        @else
            <div class="flex h-full items-center justify-center bg-gradient-to-br from-indigo-900 via-slate-900 to-fuchsia-900 text-4xl font-bold text-white/70">CD</div>
        @endif
    </div>
    <div class="flex flex-1 flex-col p-5">
        <p class="text-xs font-semibold uppercase tracking-wide text-indigo-300">{{ $event->start_date->timezone('Asia/Jakarta')->translatedFormat('D, d M Y · H:i') }} WIB</p>
        <h3 class="mt-2 line-clamp-2 text-lg font-semibold leading-snug">{{ $event->name }}</h3>
        <p class="mt-1 text-sm text-slate-400">{{ $event->venue->name }} · {{ $event->venue->city }}</p>
        @php($startingPrice = $event->ticketCategories->min('price'))
        <p class="mt-auto pt-4 text-sm text-slate-400">
            Mulai <span class="font-semibold text-emerald-300">{{ $startingPrice === null ? 'Belum tersedia' : \App\Support\Formatters::rupiah($startingPrice) }}</span>
        </p>
    </div>
</a>
