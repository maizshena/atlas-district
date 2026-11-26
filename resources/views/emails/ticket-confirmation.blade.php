<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Konfirmasi tiket</title>
</head>
<body style="margin:0;background:#000000;padding:32px 16px;font-family:Arial,Helvetica,sans-serif;color:#ffffff;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;margin:0 auto;background:#0d0d0d;border-radius:24px;border:1px solid rgba(255,255,255,0.10);">
        <tr>
            <td style="padding:28px;">
                <p style="margin:0;font-size:13px;letter-spacing:2px;text-transform:uppercase;color:#59dc80;">Pembayaran berhasil</p>
                <h1 style="margin:12px 0 0;font-size:22px;color:#ffffff;">Tiket Anda sudah siap</h1>
                <p style="margin:12px 0 0;font-size:14px;color:#999999;">Nomor pesanan <strong style="color:#ffffff;">{{ $order->order_number }}</strong></p>

                @foreach ($order->items as $item)
                    <div style="margin-top:20px;padding:16px;border:1px solid rgba(255,255,255,0.10);border-radius:16px;">
                        <p style="margin:0;font-size:16px;font-weight:bold;color:#ffffff;">{{ $item->ticketCategory->event->name }}</p>
                        <p style="margin:6px 0 0;font-size:13px;color:#999999;">{{ $item->ticketCategory->event->venue->name }} &middot; {{ $item->ticketCategory->event->start_date->timezone('Asia/Jakarta')->format('d/m/Y H:i') }} WIB</p>
                        <p style="margin:6px 0 0;font-size:13px;color:#999999;">{{ $item->ticketCategory->name }} &times; {{ $item->quantity }}</p>
                    </div>
                @endforeach

                <h2 style="margin:24px 0 8px;font-size:15px;color:#ffffff;">Kode tiket</h2>
                @foreach ($order->tickets as $ticket)
                    <p style="margin:0 0 6px;font-family:Menlo,Consolas,monospace;font-size:16px;letter-spacing:3px;color:#ffffff;">{{ $ticket->ticket_code }}</p>
                @endforeach

                <p style="margin:20px 0 0;font-size:13px;line-height:1.6;color:#999999;">Gambar QR tiket terlampir. Tunjukkan QR saat memasuki venue. Setiap tiket hanya dapat dipindai satu kali.</p>
                <p style="margin:16px 0 0;font-size:13px;color:#999999;">Total dibayar: <strong style="color:#59dc80;">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong></p>
            </td>
        </tr>
        <x-mail.layout title="Konfirmasi tiket" :width="560">
            <p style="margin:0;font-size:13px;letter-spacing:2px;text-transform:uppercase;color:#59dc80;">Pembayaran berhasil</p>
            <h1 style="margin:12px 0 0;font-size:22px;color:#ffffff;">Tiket Anda sudah siap</h1>
            <x-mail.muted :size="14">Nomor pesanan <strong style="color:#ffffff;">{{ $order->order_number }}</strong></x-mail.muted>

            @foreach ($order->items as $item)
                <div style="margin-top:20px;padding:16px;border:1px solid rgba(255,255,255,0.10);border-radius:16px;">
                    <p style="margin:0;font-size:16px;font-weight:bold;color:#ffffff;">{{ $item->ticketCategory->event->name }}</p>
                    <x-mail.muted :top="6">{{ $item->ticketCategory->event->venue->name }} &middot; {{ \App\Support\Formatters::jakartaDateTime($item->ticketCategory->event->start_date) }}</x-mail.muted>
                    <x-mail.muted :top="6">{{ $item->ticketCategory->name }} &times; {{ $item->quantity }}</x-mail.muted>
                </div>
            @endforeach

            <h2 style="margin:24px 0 8px;font-size:15px;color:#ffffff;">Kode tiket</h2>
            @foreach ($order->tickets as $ticket)
                <p style="margin:0 0 6px;font-family:Menlo,Consolas,monospace;font-size:16px;letter-spacing:3px;color:#ffffff;">{{ $ticket->ticket_code }}</p>
            @endforeach

            <x-mail.muted :top="20">Gambar QR tiket terlampir. Tunjukkan QR saat memasuki venue. Setiap tiket hanya dapat dipindai satu kali.</x-mail.muted>
            <x-mail.muted :top="16">Total dibayar: <strong style="color:#59dc80;">{{ \App\Support\Formatters::rupiah($order->total_amount) }}</strong></x-mail.muted>
        </x-mail.layout>
    </table>
</body>
</html>
