<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class OtpMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public string $code, public Carbon $expiresAt) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Kode verifikasi akun');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.otp', with: ['code' => $this->code, 'expiresAt' => $this->expiresAt]);
    }
}
