<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Konfirmasi tiket</title>
</head>
<body style="margin:0;background:#0f172a;padding:32px 16px;font-family:Arial,Helvetica,sans-serif;color:#e2e8f0;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;margin:0 auto;background:#111c33;border-radius:16px;border:1px solid #1e293b;">
        <tr>
            <td style="padding:28px;">
                <p style="margin:0;font-size:13px;letter-spacing:2px;text-transform:uppercase;color:#34d399;">Pembayaran berhasil</p>
                <h1 style="margin:12px 0 0;font-size:22px;color:#f8fafc;">Tiket Anda sudah siap</h1>
                <p style="margin:12px 0 0;font-size:14px;color:#94a3b8;">Nomor pesanan <strong style="color:#e2e8f0;">{{ $order->order_number }}</strong></p>

                @foreach ($order->items as $item)
                    <div style="margin-top:20px;padding:16px;border:1px solid #1e293b;border-radius:12px;">
                        <p style="margin:0;font-size:16px;font-weight:bold;color:#f8fafc;">{{ $item->ticketCategory->event->name }}</p>
                        <p style="margin:6px 0 0;font-size:13px;color:#94a3b8;">{{ $item->ticketCategory->event->venue->name }} &middot; {{ $item->ticketCategory->event->start_date->timezone('Asia/Jakarta')->format('d/m/Y H:i') }} WIB</p>
                        <p style="margin:6px 0 0;font-size:13px;color:#94a3b8;">{{ $item->ticketCategory->name }} &times; {{ $item->quantity }}</p>
                    </div>
                @endforeach

                <h2 style="margin:24px 0 8px;font-size:15px;color:#f8fafc;">Kode tiket</h2>
                @foreach ($order->tickets as $ticket)
                    <p style="margin:0 0 6px;font-family:Menlo,Consolas,monospace;font-size:16px;letter-spacing:3px;color:#a5b4fc;">{{ $ticket->ticket_code }}</p>
                @endforeach

                <p style="margin:20px 0 0;font-size:13px;line-height:1.6;color:#94a3b8;">Gambar QR tiket terlampir. Tunjukkan QR saat memasuki venue. Setiap tiket hanya dapat dipindai satu kali.</p>
                <p style="margin:16px 0 0;font-size:13px;color:#94a3b8;">Total dibayar: <strong style="color:#34d399;">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong></p>
            </td>
        </tr>
    </table>
</body>
</html>
