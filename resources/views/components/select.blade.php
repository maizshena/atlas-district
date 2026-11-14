@props(['label' => null, 'name'])
<label class="block">
    @if ($label)
        <span class="mb-1.5 block text-sm font-medium text-slate-300">{{ $label }}</span>
    @endif
    <select name="{{ $name }}" {{ $attributes->merge(['class' => 'w-full rounded-xl border border-white/15 bg-slate-950 px-3.5 py-2.5 text-sm text-slate-100 focus:border-indigo-400 focus:outline-none']) }}>
        {{ $slot }}
    </select>
    @error($name)
        <span class="mt-1.5 block text-xs text-rose-300">{{ $message }}</span>
    @enderror
</label>
