<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        'is_edited',
        'edited_by',
        'edited_at',
    ];

    protected function casts(): array
    {
        return [
            'nutritional_goals' => 'array',
            'days'              => 'array',
            'warnings'          => 'array',
            'is_edited'         => 'boolean',
            'edited_at'         => 'datetime',
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

    /** @return BelongsTo<User, $this> */
    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'edited_by');
    }

    /** @return HasMany<DietPlanDelivery, $this> */
    public function deliveries(): HasMany
    {
        return $this->hasMany(DietPlanDelivery::class, 'diet_plan_id');
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
