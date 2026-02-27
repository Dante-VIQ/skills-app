<?php

namespace App\Mail;

use App\Models\TutorInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TutorInvitationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $invitation;
    
    public function __construct(TutorInvitation $invitation)
    {
        $this->invitation = $invitation;
    }
    
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invitation to Join SkillDNA as a Tutor',
        );
    }
    
    public function content(): Content
    {
        return new Content(
            view: 'emails.tutor-invitation',
            with: [
                'invitation' => $this->invitation,
                'url' => route('register') . '?role=tutor',
            ],
        );
    }
}