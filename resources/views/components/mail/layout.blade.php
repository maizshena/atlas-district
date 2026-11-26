{{-- resources/views/components/mail/layout.blade.php kerangka email gelap bersama --}}
@props(['title', 'width' => 480])
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
</head>
<body style="margin:0;background:#000000;padding:32px 16px;font-family:Arial,Helvetica,sans-serif;color:#ffffff;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:{{ (int) $width }}px;margin:0 auto;background:#0d0d0d;border-radius:24px;border:1px solid rgba(255,255,255,0.10);">
        <tr>
            <td style="padding:28px;">
                {{ $slot }}
            </td>
        </tr>
    </table>
</body>
</html>