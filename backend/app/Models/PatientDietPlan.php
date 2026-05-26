<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientDietPlan extends Model
{
    /** @use HasFactory<\Database\Factories\PatientDietPlanFactory> */
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'generated_by',
        'status',
        'rationale',
        'daily_calories',
        'nutritional_goals',
        'days',
        'warnings',
        'failure_reason',
    ];

    protected function casts(): array
    {
        return [
            'nutritional_goals' => 'array',
            'days'              => 'array',
            'warnings'          => 'array',
        ];
    }

    /** @return BelongsTo<Patient, $this> */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /** @return BelongsTo<User, $this> */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function scopeCompleted(Builder $query): void
    {
        $query->where('status', 'completed');
    }

    public function scopeLatestCompleted(Builder $query): void
    {
        $query->where('status', 'completed')->latest();
    }
}
