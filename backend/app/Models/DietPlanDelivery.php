<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DietPlanDelivery extends Model
{
    /** @use HasFactory<\Database\Factories\DietPlanDeliveryFactory> */
    use HasFactory;
    protected $table = 'diet_plan_deliveries';

    protected $fillable = [
        'diet_plan_id',
        'sent_by',
        'recipient_email',
        'status',
        'failure_reason',
    ];

    /** @return BelongsTo<PatientDietPlan, $this> */
    public function dietPlan(): BelongsTo
    {
        return $this->belongsTo(PatientDietPlan::class);
    }

    /** @return BelongsTo<User, $this> */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }
}
