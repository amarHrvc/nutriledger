<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientSocioeconomic extends Model
{
    use HasFactory;

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

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
