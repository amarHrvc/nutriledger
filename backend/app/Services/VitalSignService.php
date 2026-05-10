<?php

namespace App\Services;

use App\Models\Visit;
use App\Models\VitalSign;

class VitalSignService
{
    public function record(Visit $visit, array $data): VitalSign
    {
        $data['bmi'] = $this->computeBmi($data['weight'] ?? null, $data['height'] ?? null);

        return $visit->vitalSign()->create($data);
    }

    public function update(VitalSign $vitalSign, array $data): VitalSign
    {
        $weight = array_key_exists('weight', $data) ? $data['weight'] : $vitalSign->weight;
        $height = array_key_exists('height', $data) ? $data['height'] : $vitalSign->height;

        $data['bmi'] = $this->computeBmi($weight, $height);

        $vitalSign->update($data);

        return $vitalSign;
    }

    public function delete(VitalSign $vitalSign): void
    {
        $vitalSign->delete();
    }

    private function computeBmi(mixed $weight, mixed $height): ?float
    {
        if ($weight === null || $height === null) {
            return null;
        }

        $heightM = (float) $height / 100;

        if ($heightM == 0.0) {
            return null;
        }

        return round((float) $weight / ($heightM * $heightM), 2);
    }
}
