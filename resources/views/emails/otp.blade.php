<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Kode verifikasi</title>
</head>
<body style="margin:0;background:#000000;padding:32px 16px;font-family:Arial,Helvetica,sans-serif;color:#ffffff;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:480px;margin:0 auto;background:#0d0d0d;border-radius:24px;border:1px solid rgba(255,255,255,0.10);">
        <tr>
            <td style="padding:28px;">
                <p style="margin:0;font-size:13px;letter-spacing:2px;text-transform:uppercase;color:#ffffff;">Concert District</p>
                <h1 style="margin:12px 0 0;font-size:22px;color:#ffffff;">Verifikasi email Anda</h1>
                <p style="margin:12px 0 0;font-size:14px;line-height:1.6;color:#999999;">Gunakan kode berikut untuk mengaktifkan akun Anda:</p>
                <p style="margin:20px 0;padding:16px;text-align:center;font-size:30px;font-weight:bold;letter-spacing:10px;color:#000000;background:#ffffff;border-radius:16px;">{{ $code }}</p>
                <p style="margin:0;font-size:13px;color:#999999;">Kode berlaku sampai {{ $expiresAt->timezone('Asia/Jakarta')->format('d/m/Y H:i') }} WIB.</p>
                <p style="margin:16px 0 0;font-size:12px;color:#999999;">Jika Anda tidak mendaftar, abaikan email ini.</p>
            </td>
        </tr>
        <x-mail.layout title="Kode verifikasi" :width="480">
            <p style="margin:0;font-size:13px;letter-spacing:2px;text-transform:uppercase;color:#ffffff;">Concert District</p>
            <h1 style="margin:12px 0 0;font-size:22px;color:#ffffff;">Verifikasi email Anda</h1>
            <x-mail.muted :size="14">Gunakan kode berikut untuk mengaktifkan akun Anda:</x-mail.muted>
            <p style="margin:20px 0;padding:16px;text-align:center;font-size:30px;font-weight:bold;letter-spacing:10px;color:#000000;background:#ffffff;border-radius:16px;">{{ $code }}</p>
            <x-mail.muted :top="0">Kode berlaku sampai {{ \App\Support\Formatters::jakartaDateTime($expiresAt) }}.</x-mail.muted>
            <x-mail.muted :top="16" :size="12">Jika Anda tidak mendaftar, abaikan email ini.</x-mail.muted>
        </x-mail.layout>
    </table>
</body>
</html>
