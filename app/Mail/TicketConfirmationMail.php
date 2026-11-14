<?php

namespace App\Mail;

use App\Models\Order;
use App\Services\QrCodeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketConfirmationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 10;

    public function __construct(public Order $order) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Konfirmasi tiket '.$this->order->order_number);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.ticket-confirmation', with: ['order' => $this->order]);
    }

    public function attachments(): array
    {
        $qr = app(QrCodeService::class);

        return $this->order->tickets->map(fn ($ticket) => Attachment::fromData(
            fn () => base64_decode(substr($qr->dataUri($ticket->ticket_code), strlen('data:image/png;base64,'))),
            $ticket->ticket_code.'.png',
        )->withMime('image/png'))->all();
    }
}
