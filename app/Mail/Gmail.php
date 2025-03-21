<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

use App\Models\User;

class Gmail extends Mailable
{
    use Queueable, SerializesModels;

    protected User $user;
    protected string $url;
    protected int $codigo;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user,string $url,int $codigo)
    {
        $this->user = $user;
        $this->url = $url;
        $this->codigo = $codigo;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Codigo Activacion',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'activacion',
            with: ['user' => $this->user->name,
                   'url' => $this->url,
                   'codigo' => $this->codigo
                   ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
