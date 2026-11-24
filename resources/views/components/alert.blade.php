{{-- resources/views/components/alert.blade.php pesan sesi dan kesalahan validasi --}}
@php
    $messages = collect();
    if (session('status')) {
        $messages->push(['type' => 'success', 'text' => session('status')]);
    }
    foreach (['status' => 'success', 'success' => 'success', 'error' => 'error'] as $key => $type) {
        if (session($key) && $key !== 'status') {
            $messages->push(['type' => $type, 'text' => session($key)]);
        }
    }
    if ($errors->any()) {
        foreach ($errors->all() as $message) {
            $messages->push(['type' => 'error', 'text' => $message]);
        }
    }
@endphp
@if ($messages->isNotEmpty())
    <div class="mb-6 space-y-2" aria-live="polite">
        @foreach ($messages->unique('text') as $message)
            <div @class([
                'flex items-start gap-3 rounded-tile border bg-surface-1 px-4 py-3 text-caption',
                'border-green/40' => $message['type'] === 'success',
                'border-pink/40' => $message['type'] === 'error',
            ]) @if ($message['type'] === 'error') role="alert" @endif>
                <span @class(['mt-0.5', 'text-green' => $message['type'] === 'success', 'text-pink' => $message['type'] === 'error'])>
                    <x-icon :name="$message['type'] === 'success' ? 'check' : 'alert'" :size="16" />
                </span>
                <span class="text-white">{{ $message['text'] }}</span>
            </div>
        @endforeach
    </div>
@endif
