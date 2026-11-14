@props(['label' => null, 'name', 'rows' => 4])
<label class="block">
    @if ($label)
        <span class="mb-1.5 block text-sm font-medium text-slate-300">{{ $label }}</span>
    @endif
    <textarea name="{{ $name }}" rows="{{ $rows }}" {{ $attributes->merge(['class' => 'w-full rounded-xl border bg-slate-950 px-3.5 py-2.5 text-sm text-slate-100 placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 '.($errors->has($name) ? 'border-rose-400/60' : 'border-white/15')]) }}>{{ old($name, $slot) }}</textarea>
    @error($name)
        <span class="mt-1.5 block text-xs text-rose-300">{{ $message }}</span>
    @enderror
</label>
