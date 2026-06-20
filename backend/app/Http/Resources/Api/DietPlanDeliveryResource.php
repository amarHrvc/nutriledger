<?php

namespace App\Http\Resources\Api;

use App\Models\DietPlanDelivery;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin DietPlanDelivery
 */
class DietPlanDeliveryResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'status' => $this->status,
            'recipientEmail' => $this->recipient_email,
            'failureReason' => $this->when($this->status === 'failed', $this->failure_reason),
            'createdAt' => $this->created_at?->toDateTimeString(),
        ];
    }
}
