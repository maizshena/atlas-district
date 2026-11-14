<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Kode verifikasi</title>
</head>
<body style="margin:0;background:#0f172a;padding:32px 16px;font-family:Arial,Helvetica,sans-serif;color:#e2e8f0;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:480px;margin:0 auto;background:#111c33;border-radius:16px;border:1px solid #1e293b;">
        <tr>
            <td style="padding:28px;">
                <p style="margin:0;font-size:13px;letter-spacing:2px;text-transform:uppercase;color:#818cf8;">Concert District</p>
                <h1 style="margin:12px 0 0;font-size:22px;color:#f8fafc;">Verifikasi email Anda</h1>
                <p style="margin:12px 0 0;font-size:14px;line-height:1.6;color:#94a3b8;">Gunakan kode berikut untuk mengaktifkan akun Anda:</p>
                <p style="margin:20px 0;padding:16px;text-align:center;font-size:30px;font-weight:bold;letter-spacing:10px;color:#0f172a;background:#a5b4fc;border-radius:12px;">{{ $code }}</p>
                <p style="margin:0;font-size:13px;color:#94a3b8;">Kode berlaku sampai {{ $expiresAt->timezone('Asia/Jakarta')->format('d/m/Y H:i') }} WIB.</p>
                <p style="margin:16px 0 0;font-size:12px;color:#64748b;">Jika Anda tidak mendaftar, abaikan email ini.</p>
            </td>
        </tr>
    </table>
</body>
</html>
