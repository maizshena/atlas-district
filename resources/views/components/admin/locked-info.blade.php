@props(['event'])

<div class="rounded-input border border-border-token bg-surface-1 p-4">
    <p class="text-caption font-medium text-white">Data terkunci</p>
    <p class="mt-1 text-micro text-gray">Venue, jadwal, artis, dan tiket hanya dapat diubah admin. Hubungi admin bila ada perubahan.</p>
    <dl class="mt-3 grid gap-2 text-caption sm:grid-cols-2">
        <div><dt class="text-gray">Venue</dt><dd class="text-white">{{ $event->venue->name }}</dd></div>
        <div><dt class="text-gray">Slug</dt><dd class="num text-white">{{ $event->slug }}</dd></div>
        <div><dt class="text-gray">Mulai</dt><dd class="text-white">{{ \App\Support\Formatters::jakartaDateTime($event->start_date) }}</dd></div>
        <div><dt class="text-gray">Selesai</dt><dd class="text-white">{{ \App\Support\Formatters::jakartaDateTime($event->end_date) }}</dd></div>
        <div class="sm:col-span-2"><dt class="text-gray">Artis</dt><dd class="text-white">{{ $event->artists->pluck('name')->join(', ') ?: 'Segera diumumkan' }}</dd></div>
    </dl>
</div>