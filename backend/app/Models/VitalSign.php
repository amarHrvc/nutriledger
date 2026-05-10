<?php

namespace App\Models;

use Database\Factories\VitalSignFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $visit_id
 * @property int|null $systolic_bp
 * @property int|null $diastolic_bp
 * @property int|null $heart_rate
 * @property string|null $temperature
 * @property string|null $weight
 * @property string|null $height
 * @property string|null $bmi
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property-read Visit $visit
 * @property-read array $computed_flags
 */
class VitalSign extends Model
{
    /** @use HasFactory<VitalSignFactory> */
    use HasFactory;

    /** Set by VitalSignController::show() to attach previous visit deltas. */
    public ?VitalSign $previousVitals = null;

    protected $fillable = [
        'visit_id',
        'systolic_bp',
        'diastolic_bp',
        'heart_rate',
        'temperature',
        'weight',
        'height',
        'bmi',
    ];

    protected function casts(): array
    {
        return [
            'systolic_bp' => 'integer',
            'diastolic_bp' => 'integer',
            'heart_rate' => 'integer',
            'temperature' => 'decimal:1',
            'weight' => 'decimal:2',
            'height' => 'decimal:2',
            'bmi' => 'decimal:2',
        ];
    }

    /** @return BelongsTo<Visit, $this> */
    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }

    /** @return array<int, array<string, mixed>> */
    public function getComputedFlagsAttribute(): array
    {
        $flags = [];

        if ($this->systolic_bp !== null && ($this->systolic_bp < 90 || $this->systolic_bp > 140)) {
            $flags[] = ['field' => 'systolicBp', 'value' => $this->systolic_bp, 'threshold' => '90–140 mmHg'];
        }

        if ($this->diastolic_bp !== null && ($this->diastolic_bp < 60 || $this->diastolic_bp > 90)) {
            $flags[] = ['field' => 'diastolicBp', 'value' => $this->diastolic_bp, 'threshold' => '60–90 mmHg'];
        }

        if ($this->heart_rate !== null && ($this->heart_rate < 50 || $this->heart_rate > 100)) {
            $flags[] = ['field' => 'heartRate', 'value' => $this->heart_rate, 'threshold' => '50–100 bpm'];
        }

        if ($this->temperature !== null && ((float) $this->temperature < 36.0 || (float) $this->temperature > 37.5)) {
            $flags[] = ['field' => 'temperature', 'value' => $this->temperature, 'threshold' => '36.0–37.5 °C'];
        }

        if ($this->bmi !== null) {
            $bmi = (float) $this->bmi;
            if ($bmi < 18.5) {
                $flags[] = ['field' => 'bmi', 'value' => $this->bmi, 'category' => 'underweight'];
            } elseif ($bmi >= 30.0) {
                $flags[] = ['field' => 'bmi', 'value' => $this->bmi, 'category' => 'obese'];
            } elseif ($bmi >= 25.0) {
                $flags[] = ['field' => 'bmi', 'value' => $this->bmi, 'category' => 'overweight'];
            }
        }

        return $flags;
    }
}
