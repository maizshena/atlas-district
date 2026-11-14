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
                'rounded-xl border px-4 py-3 text-sm',
                'border-emerald-400/30 bg-emerald-400/10 text-emerald-200' => $message['type'] === 'success',
                'border-rose-400/30 bg-rose-400/10 text-rose-200' => $message['type'] === 'error',
            ])>{{ $message['text'] }}</div>
        @endforeach
    </div>
@endif
