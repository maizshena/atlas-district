{{-- resources/views/admin/gate.blade.php halaman validasi tiket dengan panel hasil --}}
@extends('layouts.admin', ['title' => 'Validasi gate'])

@section('content')
    <div class="mx-auto max-w-4xl">
        <x-bento.page-header eyebrow="Pintu masuk" title="Validasi tiket" description="Pilih event, klik kolom kode tiket, lalu pindai barcode atau ketik kode manual." />

        <x-bento.grid>
            <x-bento.tile span="2x2" tone="raised">
                <form method="POST" action="{{ route('admin.gate.validate') }}">
                    @csrf

                    <x-select name="event_id" label="Event" required>
                        <option value="">Pilih event yang dijaga</option>
                        @foreach ($events as $event)
                            <option value="{{ $event->id }}" @selected((int) old('event_id', $selectedEventId) === $event->id)>
                                {{ $event->name }} ({{ $event->start_date->format('d M Y') }})
                            </option>
                        @endforeach
                    </x-select>

                    <x-input label="Kode tiket" name="ticket_code" :value="old('ticket_code')" autocomplete="off" inputmode="numeric" placeholder="0000-0000-0000" maxlength="14" class="num mt-4 text-center text-title tracking-[0.3em]" required />

                    <x-button type="submit" class="mt-4 w-full" data-submit>Validasi tiket</x-button>
                </form>
            </x-bento.tile>

            <x-bento.tile span="2x2" class="flex flex-col justify-center">
                <x-bento.icon-badge icon="scan" size="lg" tone="surface" />
                <p class="mt-3 text-heading font-semibold text-white">Cara pakai</p>
                <ol class="mt-2 space-y-1.5 text-caption text-gray">
                    <li>1. Pilih event yang sedang dijaga.</li>
                    <li>2. Pindai QR atau ketik kode tiket.</li>
                    <li>3. Cocokkan warna hasil sebelum meloloskan penonton.</li>
                </ol>
            </x-bento.tile>

            @if (session('gate_result'))
                <x-gate.result-panel :result="session('gate_result')" :gate-event-name="session('gate_event_name')" />
            @endif
        </x-bento.grid>
    </div>
@endsection