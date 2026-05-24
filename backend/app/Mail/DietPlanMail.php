<?php

namespace App\Mail;

use App\Models\PatientDietPlan;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DietPlanMail extends Mailable
{
    use SerializesModels;

    public function __construct(
        public PatientDietPlan $plan
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Personalised Diet Plan',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.diet-plan',
        );
    }
}
