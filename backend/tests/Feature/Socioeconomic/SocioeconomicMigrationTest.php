<?php

use Illuminate\Support\Facades\Schema;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('patient_socioeconomic table exists', function () {
    expect(Schema::hasTable('patient_socioeconomic'))->toBeTrue();
});

test('patient_socioeconomic table has patient_id column', function () {
    expect(Schema::hasColumn('patient_socioeconomic', 'patient_id'))->toBeTrue();
});

test('patient_socioeconomic table has marital_status column', function () {
    expect(Schema::hasColumn('patient_socioeconomic', 'marital_status'))->toBeTrue();
});

test('patient_socioeconomic table has number_of_dependents column', function () {
    expect(Schema::hasColumn('patient_socioeconomic', 'number_of_dependents'))->toBeTrue();
});

test('patient_socioeconomic table has living_arrangement column', function () {
    expect(Schema::hasColumn('patient_socioeconomic', 'living_arrangement'))->toBeTrue();
});

test('patient_socioeconomic table has employment_status column', function () {
    expect(Schema::hasColumn('patient_socioeconomic', 'employment_status'))->toBeTrue();
});

test('patient_socioeconomic table has occupation column', function () {
    expect(Schema::hasColumn('patient_socioeconomic', 'occupation'))->toBeTrue();
});

test('patient_socioeconomic table has income_level column', function () {
    expect(Schema::hasColumn('patient_socioeconomic', 'income_level'))->toBeTrue();
});

test('patient_socioeconomic table has has_health_insurance column', function () {
    expect(Schema::hasColumn('patient_socioeconomic', 'has_health_insurance'))->toBeTrue();
});

test('patient_socioeconomic table has education_level column', function () {
    expect(Schema::hasColumn('patient_socioeconomic', 'education_level'))->toBeTrue();
});

test('patient_socioeconomic table has smoking_status column', function () {
    expect(Schema::hasColumn('patient_socioeconomic', 'smoking_status'))->toBeTrue();
});

test('patient_socioeconomic table has alcohol_consumption column', function () {
    expect(Schema::hasColumn('patient_socioeconomic', 'alcohol_consumption'))->toBeTrue();
});

test('patient_socioeconomic table has physical_activity_level column', function () {
    expect(Schema::hasColumn('patient_socioeconomic', 'physical_activity_level'))->toBeTrue();
});

test('patient_socioeconomic table has has_family_support column', function () {
    expect(Schema::hasColumn('patient_socioeconomic', 'has_family_support'))->toBeTrue();
});

test('patient_socioeconomic table has has_caregiver column', function () {
    expect(Schema::hasColumn('patient_socioeconomic', 'has_caregiver'))->toBeTrue();
});

test('patient_socioeconomic table has transportation_access column', function () {
    expect(Schema::hasColumn('patient_socioeconomic', 'transportation_access'))->toBeTrue();
});

test('patient_socioeconomic table has food_security_status column', function () {
    expect(Schema::hasColumn('patient_socioeconomic', 'food_security_status'))->toBeTrue();
});

test('patient_socioeconomic table has dietary_restrictions_cultural column', function () {
    expect(Schema::hasColumn('patient_socioeconomic', 'dietary_restrictions_cultural'))->toBeTrue();
});

test('patient_socioeconomic table has additional_notes column', function () {
    expect(Schema::hasColumn('patient_socioeconomic', 'additional_notes'))->toBeTrue();
});

test('patient_socioeconomic table has created_at column', function () {
    expect(Schema::hasColumn('patient_socioeconomic', 'created_at'))->toBeTrue();
});

test('patient_socioeconomic table has updated_at column', function () {
    expect(Schema::hasColumn('patient_socioeconomic', 'updated_at'))->toBeTrue();
});

test('has_health_insurance column defaults to false', function () {
    $columns = \Illuminate\Support\Facades\DB::select("PRAGMA table_info('patient_socioeconomic')");
    $column = collect($columns)->firstWhere('name', 'has_health_insurance');

    expect($column)->not->toBeNull()
        ->and((int) $column->dflt_value)->toBe(0);
});

test('has_family_support column defaults to false', function () {
    $columns = \Illuminate\Support\Facades\DB::select("PRAGMA table_info('patient_socioeconomic')");
    $column = collect($columns)->firstWhere('name', 'has_family_support');

    expect($column)->not->toBeNull()
        ->and((int) $column->dflt_value)->toBe(0);
});

test('has_caregiver column defaults to false', function () {
    $columns = \Illuminate\Support\Facades\DB::select("PRAGMA table_info('patient_socioeconomic')");
    $column = collect($columns)->firstWhere('name', 'has_caregiver');

    expect($column)->not->toBeNull()
        ->and((int) $column->dflt_value)->toBe(0);
});

test('patient_id is a foreign key referencing patients table', function () {
    $foreignKeys = \Illuminate\Support\Facades\DB::select("PRAGMA foreign_key_list('patient_socioeconomic')");
    $fk = collect($foreignKeys)->firstWhere('from', 'patient_id');

    expect($fk)->not->toBeNull()
        ->and($fk->table)->toBe('patients');
});

test('patient_id has a unique constraint enforcing one-to-one relationship', function () {
    $indexes = \Illuminate\Support\Facades\DB::select("PRAGMA index_list('patient_socioeconomic')");

    $uniqueOnPatientId = collect($indexes)->filter(function ($index) {
        if (! $index->unique) {
            return false;
        }
        $indexName = addslashes((string) $index->name);
        $indexInfo = \Illuminate\Support\Facades\DB::select("PRAGMA index_info('{$indexName}')");

        return collect($indexInfo)->contains('name', 'patient_id');
    });

    expect($uniqueOnPatientId)->not->toBeEmpty();
});
