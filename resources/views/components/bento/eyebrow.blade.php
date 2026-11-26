{{-- resources/views/components/bento/eyebrow.blade.php label kecil kapital di atas judul atau nilai --}}
@props(['color' => 'text-gray'])

<p {{ $attributes->class(['text-micro font-semibold uppercase tracking-wider', $color]) }}>{{ $slot }}</p>