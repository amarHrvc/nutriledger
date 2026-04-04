<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $patient_id
 * @property string|null $marital_status
 * @property int $number_of_dependents
 * @property string|null $living_arrangement
 * @property string|null $employment_status
 * @property string|null $occupation
 * @property string|null $income_level
 * @property bool $has_health_insurance
 * @property string|null $education_level
 * @property string|null $smoking_status
 * @property string|null $alcohol_consumption
 * @property string|null $physical_activity_level
 * @property bool $has_family_support
 * @property bool $has_caregiver
 * @property string|null $transportation_access
 * @property string|null $food_security_status
 * @property string|null $dietary_restrictions_cultural
 * @property string|null $additional_notes
 * @property ?Carbon $deleted_at
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property-read Patient $patient
 */
class PatientSocioeconomic extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'patient_socioeconomic';

    protected $fillable = [
        'marital_status',
        'number_of_dependents',
        'living_arrangement',
        'employment_status',
        'occupation',
        'income_level',
        'has_health_insurance',
        'education_level',
        'smoking_status',
        'alcohol_consumption',
        'physical_activity_level',
        'has_family_support',
        'has_caregiver',
        'transportation_access',
        'food_security_status',
        'dietary_restrictions_cultural',
        'additional_notes',
    ];

    protected function casts(): array
    {
        return [
            'has_health_insurance' => 'boolean',
            'has_family_support' => 'boolean',
            'has_caregiver' => 'boolean',
            'number_of_dependents' => 'integer',
        ];
    }

    /** @return BelongsTo<Patient, $this> */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
