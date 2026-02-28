<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Convert enum columns to string to allow updated enum values
 * without DB-level CHECK constraint conflicts.
 * Validation is enforced at the application layer.
 */
return new class extends Migration
{
    public function up(): void
    {
        // SQLite does not support ALTER COLUMN, so we recreate the table.
        DB::statement('PRAGMA foreign_keys = OFF');

        DB::statement('
            CREATE TABLE patient_socioeconomic_new (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                patient_id INTEGER NOT NULL UNIQUE REFERENCES patients(id) ON DELETE CASCADE,
                marital_status VARCHAR(50) NULL,
                number_of_dependents INTEGER NULL,
                living_arrangement VARCHAR(50) NULL,
                employment_status VARCHAR(50) NULL,
                occupation VARCHAR(255) NULL,
                income_level VARCHAR(50) NULL,
                has_health_insurance TINYINT(1) NOT NULL DEFAULT 0,
                education_level VARCHAR(50) NULL,
                smoking_status VARCHAR(50) NULL,
                alcohol_consumption VARCHAR(50) NULL,
                physical_activity_level VARCHAR(50) NULL,
                has_family_support TINYINT(1) NOT NULL DEFAULT 0,
                has_caregiver TINYINT(1) NOT NULL DEFAULT 0,
                transportation_access VARCHAR(50) NULL,
                food_security_status VARCHAR(50) NULL,
                dietary_restrictions_cultural TEXT NULL,
                additional_notes TEXT NULL,
                created_at DATETIME NULL,
                updated_at DATETIME NULL
            )
        ');

        DB::statement('
            INSERT INTO patient_socioeconomic_new
            SELECT id, patient_id, marital_status, number_of_dependents, living_arrangement,
                   employment_status, occupation, income_level, has_health_insurance,
                   education_level, smoking_status, alcohol_consumption, physical_activity_level,
                   has_family_support, has_caregiver, transportation_access, food_security_status,
                   dietary_restrictions_cultural, additional_notes, created_at, updated_at
            FROM patient_socioeconomic
        ');

        DB::statement('DROP TABLE patient_socioeconomic');
        DB::statement('ALTER TABLE patient_socioeconomic_new RENAME TO patient_socioeconomic');

        DB::statement('PRAGMA foreign_keys = ON');
    }

    public function down(): void
    {
        // Irreversible in terms of CHECK constraints, but column structure is preserved.
    }
};
