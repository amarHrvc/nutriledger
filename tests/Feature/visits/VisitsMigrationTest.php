<?php

test('visits table exists', function () {
    expect(Schema::hasTable('visits'))->toBeTrue();
});


test('visits table has expected columns', function () {
    $expectedColumns = ['id', 'patient_id', 'doctor_id', 'date', 'notes', 'created_at', 'updated_at'];
    foreach ($expectedColumns as $column) {
        expect(Schema::hasColumn('visits', $column))->toBeTrue();
    }
});


test('visits table has required columns', function () {
    $expectedColumns = ['id', 'patient_id', 'doctor_id', 'date', 'notes', 'created_at', 'updated_at'];
    expect(Schema::hasColumns('visits', $expectedColumns))->toBeTrue();
});
