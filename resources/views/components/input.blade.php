{{-- resources/views/components/input.blade.php kolom teks dengan label dan error inline --}}
@props(['label' => null, 'name', 'type' => 'text', 'value' => null, 'hint' => null])
@php($fieldId = $attributes->get('id') ?? $name)
<label for="{{ $fieldId }}" class="block">
    @if ($label)
        <span class="mb-1.5 block text-caption font-medium text-gray">{{ $label }}</span>
    @endif
    <input id="{{ $fieldId }}" type="{{ $type }}" name="{{ $name }}" value="{{ old($name, $value) }}" @if ($errors->has($name)) aria-invalid="true" aria-describedby="{{ $fieldId }}-error" @endif {{ $attributes->merge(['class' => 'min-h-11 w-full rounded-input border bg-surface-2 px-3.5 py-2.5 text-body text-white placeholder:text-gray caret-white focus:outline-none focus-visible:border-white focus-visible:ring-2 focus-visible:ring-white/40 '.($errors->has($name) ? 'border-pink' : 'border-border-token')]) }}>
    @if ($hint)
        <span class="mt-1.5 block text-micro text-gray">{{ $hint }}</span>
    @endif
    @error($name)
        <span id="{{ $fieldId }}-error" class="mt-1.5 block text-micro text-pink">{{ $message }}</span>
    @enderror
</label>
