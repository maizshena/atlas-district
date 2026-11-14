@props(['href', 'active' => false])
<a href="{{ $href }}" @class(['block rounded-lg px-3 py-2 text-sm font-medium transition', 'bg-indigo-500/15 text-indigo-200' => $active, 'text-slate-400 hover:bg-white/5 hover:text-white' => ! $active])>{{ $slot }}</a>
