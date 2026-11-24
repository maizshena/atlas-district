{{-- resources/views/components/admin/table.blade.php tabel semantik bergaya tema --}}
@props(['head' => []])
<table {{ $attributes->merge(['class' => 'w-full min-w-[640px] text-left text-caption']) }}>
    <thead class="border-b border-border-token bg-surface-2 text-micro uppercase tracking-wider text-gray">
        <tr>
            @foreach ($head as $key => $th)
                @php($cell = is_array($th) ? $th : ['label' => $th])
                <th scope="col" @class(['whitespace-nowrap px-4 py-3 font-semibold lg:px-6', 'hidden md:table-cell' => ($cell['hide'] ?? null) === 'md', 'hidden sm:table-cell' => ($cell['hide'] ?? null) === 'sm', 'hidden lg:table-cell' => ($cell['hide'] ?? null) === 'lg', 'text-right' => ($cell['align'] ?? null) === 'right'])>{{ $cell['label'] }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody class="divide-y divide-border-token">
        {{ $slot }}
    </tbody>
</table>
