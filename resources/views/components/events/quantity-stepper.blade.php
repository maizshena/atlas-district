{{-- resources/views/components/event/quantity-stepper.blade.php kontrol jumlah tiket per kategori --}}
@props(['categoryId', 'name', 'limit'])

<div class="flex items-center gap-1.5">
    <x-button variant="secondary" class="w-11 !px-0 text-heading" @click="decrease(@js($categoryId))" aria-label="Kurangi tiket {{ $name }}">&minus;</x-button>
    <input type="number" min="0" max="{{ $limit }}" x-model.number="quantities[@js($categoryId)]" @change="clamp(@js($categoryId))" class="num h-11 w-14 rounded-btn border border-border-token bg-surface-3 text-center text-caption text-white focus-visible:border-white" aria-label="Jumlah tiket {{ $name }}">
    <x-button variant="secondary" class="w-11 !px-0 text-heading" @click="increase(@js($categoryId), @js($limit))" aria-label="Tambah tiket {{ $name }}">+</x-button>
</div>