{{-- resources/views/components/admin/file-field.blade.php unggah gambar dengan pratinjau --}}
@props(['label', 'name', 'accept' => 'image/jpeg,image/png,image/webp', 'preview' => null, 'previewAlt' => '', 'hint' => 'JPG, PNG, atau WEBP, maks 5 MB.'])
@php($fieldId = $name.'_file')
<div>
    <label for="{{ $fieldId }}" class="block text-caption font-medium text-gray">{{ $label }}</label>
    @if ($preview)
        <img src="{{ $preview }}" alt="{{ $previewAlt }}" class="mt-2 h-32 w-full rounded-input border border-border-token object-cover sm:w-64">
    @endif
    <input id="{{ $fieldId }}" type="file" name="{{ $name }}" accept="{{ $accept }}" {{ $attributes->merge(['class' => 'mt-2 block w-full text-caption text-gray file:mr-3 file:min-h-11 file:rounded-btn file:border-0 file:bg-surface-3 file:px-4 file:text-caption file:font-semibold file:text-white']) }}>
    <span class="mt-1 block text-micro text-gray">{{ $hint }}</span>
    @error($name)<span class="mt-1 block text-micro text-pink">{{ $message }}</span>@enderror
</div>
