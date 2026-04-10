<?php

namespace App\Models;

use Database\Factories\PatientFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string $first_name
 * @property string $last_name
 * @property Carbon $date_of_birth
 * @property string|null $gender
 * @property string|null $address
 * @property string|null $city
 * @property string|null $postal_code
 * @property string|null $phone
 * @property string|null $emergency_contact_name
 * @property string|null $emergency_contact_phone
 * @property string|null $blood_type
 * @property string|null $allergies
 * @property string|null $medical_notes
 * @property ?Carbon $deleted_at
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property-read string $full_name
 * @property-read User $user
 * @property-read PatientSocioeconomic|null $socioeconomic
 * @property-read Collection<int, Visit> $visits
 */
class Patient extends Model
{
    /** @use HasFactory<PatientFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'address',
        'city',
        'postal_code',
        'phone',
        'emergency_contact_name',
        'emergency_contact_phone',
        'blood_type',
        'allergies',
        'medical_notes',

    ];

    protected static function booted(): void
    {
        static::deleted(function (Patient $patient) {
            $patient->socioeconomic?->delete();
        });

        static::restored(function (Patient $patient) {
            PatientSocioeconomic::withTrashed()->where('patient_id', $patient->id)->first()?->restore();
        });
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => "{$this->first_name} {$this->last_name}",
        );
    }

    /** @return HasOne<PatientSocioeconomic, $this> */
    public function socioeconomic(): HasOne
    {
        return $this->hasOne(PatientSocioeconomic::class);
    }

    /** @return HasMany<Visit, $this> */
    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class)->latest('date');
    }

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
        ];

    }
}
