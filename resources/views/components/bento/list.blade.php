{{-- resources/views/components/bento/list.blade.php isi tile daftar baris ringkas --}}
@props(['items', 'title' => null, 'empty' => 'Belum ada data.'])
<div class="flex h-full flex-col">
    @if ($title)
        <div class="flex items-center justify-between gap-2">
            <h2 class="text-heading font-semibold">{{ $title }}</h2>
            @isset($action)
                {{ $action }}
            @endisset
        </div>
    @endif
    @if ($items->isEmpty())
        <p class="py-6 text-center text-caption sub">{{ $empty }}</p>
    @else
        <ul class="mt-2 divide-y divide-border-token">
            {{ $items }}
        </ul>
    @endif
    {{ $slot }}
</div>
