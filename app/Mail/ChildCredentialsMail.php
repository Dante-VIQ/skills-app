<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class ChildCredentialsMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $parent;
    public User $child;
    public string $temporaryPassword;
    public string $loginUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(User $parent, User $child, string $temporaryPassword)
    {
        $this->parent = $parent;
        $this->child = $child;
        $this->temporaryPassword = $temporaryPassword;
        $this->loginUrl = route('child.login');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            subject: '🎉 Your Child\'s SkillDNA Account is Ready!',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.child-credentials',
        );
    }
}