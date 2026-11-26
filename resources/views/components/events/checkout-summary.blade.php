{{-- resources/views/components/event/checkout-summary.blade.php ringkasan total dan tombol beli tiket --}}
@props(['isClosed'])

<div class="mt-auto border-t border-border-token pt-4">
    <div class="flex items-baseline justify-between gap-3">
        <span class="text-caption text-gray">Total <span x-show="count() > 0" x-text="'(' + count() + ' tiket)'"></span></span>
        <strong class="num text-title text-white" x-text="format(total())">Rp 0</strong>
    </div>

    @if ($isClosed)
        <x-button variant="secondary" class="mt-3 w-full" disabled>Penjualan ditutup</x-button>
    @elseif (auth()->check())
        <x-button type="submit" class="mt-3 w-full" x-bind:disabled="submitting || count() < 1" x-text="submitting ? 'Membuat pesanan...' : 'Beli tiket'">Beli tiket</x-button>
        <p class="mt-2 text-center text-micro text-gray">Kuota ditahan <span class="num">{{ config('concert.order_ttl_minutes') }}</span> menit sampai dibayar.</p>
    @else
        <x-button :href="route('login')" class="mt-3 w-full">Masuk untuk membeli</x-button>
        <p class="mt-2 text-center text-micro text-gray">Belum punya akun? <a href="{{ route('register') }}" class="font-semibold text-white underline underline-offset-4">Daftar gratis</a>.</p>
    @endif
</div>