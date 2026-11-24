{{-- resources/views/components/admin/form-tile.blade.php wadah form admin satu tile dengan tombol aksi --}}
@props(['back' => null, 'backLabel' => 'Kembali', 'title', 'description' => null, 'action' => null, 'multipart' => false, 'method' => null, 'cancel' => null, 'submit' => 'Simpan'])
<div class="mx-auto max-w-2xl">
    @if ($back)
        <a href="{{ $back }}" class="mb-4 inline-flex min-h-11 items-center gap-1.5 text-caption text-gray hover:text-white">
            <x-icon name="arrow-left" :size="16" /> {{ $backLabel }}
        </a>
    @endif

    <x-bento.tile span="2x2" tone="raised" class="!col-span-1 !row-span-1">
        <h1 class="text-title text-white">{{ $title }}</h1>
        @if ($description)
            <p class="mt-1 text-caption text-gray">{{ $description }}</p>
        @endif

        <form method="POST" action="{{ $action }}" @if ($multipart) enctype="multipart/form-data" @endif {{ $attributes->merge(['class' => 'mt-6 space-y-5']) }}>
            @csrf
            @if ($method)
                @method($method)
            @endif

            {{ $slot }}

            <div class="flex flex-wrap justify-end gap-3 border-t border-border-token pt-5">
                @if ($cancel)
                    <x-button :href="$cancel" variant="secondary">Batal</x-button>
                @endif
                <x-button type="submit" data-submit>{{ $submit }}</x-button>
            </div>
        </form>
    </x-bento.tile>
</div>
