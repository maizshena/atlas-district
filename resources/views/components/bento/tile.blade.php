{{-- resources/views/components/bento/tile.blade.php tile dasar bento grid --}}
@props([
    'span' => '1x1',
    'tone' => 'neutral',
    'href' => null,
    'label' => null,
    'as' => null,
    'interactive' => null,
])

@php
    $spanClasses = [
        '1x1' => 'col-span-1 row-span-1',
        '2x1' => 'col-span-2 row-span-1',
        '1x2' => 'col-span-1 row-span-2',
        '2x2' => 'col-span-2 row-span-2',
        '3x2' => 'col-span-2 row-span-2 md:col-span-3 md:row-span-2',
        '4x2' => 'col-span-2 row-span-2 md:col-span-4 md:row-span-2',
        '6x2' => 'col-span-2 row-span-2 md:col-span-4 md:row-span-2 xl:col-span-6 xl:row-span-2',
    ][$span] ?? 'col-span-1 row-span-1';

    $toneClasses = match ($tone) {
        'white' => 'bg-white text-black border-transparent',
        'gray' => 'bg-gray text-black border-transparent',
        'orange' => 'tile-secondary bg-orange/14 text-orange border-orange/25',
        'green' => 'tile-secondary bg-green/14 text-green border-green/25',
        'pink' => 'tile-secondary bg-pink/14 text-pink border-pink/25',
        'blue' => 'tile-secondary bg-blue/14 text-blue border-blue/25',
        'gradient' => 'bg-linear-to-br from-orange to-pink text-black border-transparent',
        'raised' => 'bg-surface-2 text-white border-border-token',
        default => 'bg-surface-1 text-white border-border-token',
    };

    $isLink = $href !== null;
    $tag = $as ?? ($isLink ? 'a' : 'div');
    $interactive = $interactive ?? $isLink;
    $linkAttrs = $isLink ? ['href' => $href, 'aria-label' => $label] : [];
    if (! $isLink && $label !== null) {
        $linkAttrs['aria-label'] = $label;
    }
    $toneIsLight = in_array($tone, ['white', 'gray', 'gradient'], true);
@endphp

<{{ $tag }} data-reveal {{ $attributes->merge($linkAttrs)->class([
        "$spanClasses rounded-tile border p-4 transition duration-150 lg:p-6",
        $toneClasses,
        'min-h-11' => $isLink,
        'bento-reveal',
        'tile-focus bento-hover:border-border-strong active:scale-[0.98]' => $interactive && ! $toneIsLight,
        'tile-focus bento-hover:brightness-95 active:scale-[0.98]' => $interactive && $toneIsLight,
    ]) }}>
    {{ $slot }}
</{{ $tag }}>
