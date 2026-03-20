<?php

namespace App\Http\Resources\Api;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read int $id
 * @property-read string $name
 * @property-read string $email
 * @property-read string $role
 * @property-read string $patient
 * @property-read Carbon $created_at
 * @property-read ?Carbon $updated_at
 * @property-read ?Carbon $deleted_at
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        //        dump('====================================');
        //        dd($this);
        return [
            'type' => 'users',
            'id' => $this->id,
            'attributes' => [
                'name' => $this->name,
                'email' => $this->email,
                'role' => $this->role,
                'createdAt' => $this->created_at->toIso8601String(),
                'updatedAt' => $this->updated_at?->toIso8601String(),
                'deletedAt' => $this->deleted_at?->toIso8601String() ?? null,
            ],
            'relationships' => [
                'patient' => $this->whenLoaded('patient', fn () => $this->patient),
            ],
            'links' => [
                'self' => route('user.me'),
            ],
        ];
    }
}
