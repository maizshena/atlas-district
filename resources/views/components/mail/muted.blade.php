{{-- resources/views/components/mail/muted.blade.php paragraf abu untuk email --}}
@props(['top' => 12, 'size' => 13])
<p style="margin:{{ (int) $top }}px 0 0;font-size:{{ (int) $size }}px;line-height:1.6;color:#999999;">{{ $slot }}</p>