<?php

namespace App\Jobs;

use App\Mail\DietPlanMail;
use App\Models\DietPlanDelivery;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendDietPlanEmailJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public function __construct(
        public DietPlanDelivery $delivery
    ) {}

    public function handle(): void
    {
        $plan = $this->delivery->dietPlan()->with('patient', 'doctor')->first();

        try {
            Mail::to($this->delivery->recipient_email)->send(new DietPlanMail($plan));
            $this->delivery->update([
                'status' => 'sent',
            ]);
        } catch (Throwable $e) {
            $this->delivery->update([
                'status' => 'failed',
                'failure_reason' => $e->getMessage(),
            ]);
        }
    }
}
