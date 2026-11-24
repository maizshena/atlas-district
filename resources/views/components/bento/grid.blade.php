{{-- resources/views/components/bento/grid.blade.php kontainer grid bento responsif --}}
@props(['as' => 'div'])
<{{ $as }} {{ $attributes->merge(['class' => 'grid grid-cols-2 gap-3 md:grid-cols-4 md:gap-4 xl:grid-cols-6']) }}>
    {{ $slot }}
</{{ $as }}>
