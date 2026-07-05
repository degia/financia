<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DailyReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $date,
        public array $data,
        public string $symbol = 'Rp',
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Daily Financial Report - ' . \Carbon\Carbon::parse($this->date)->format('d M Y'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.daily-report',
        );
    }
}
