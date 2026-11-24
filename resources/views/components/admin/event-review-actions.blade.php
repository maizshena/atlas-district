@props(['event'])

<x-chip :tone="$event->status->tone()" {{ $attributes }}>
    <x-icon :name="$event->status->icon()" :size="12" /> {{ $event->status->label() }}
</x-chip>