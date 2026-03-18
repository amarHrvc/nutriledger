<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('patient_socioeconomic', function (Blueprint $table) {
            $table->id();

            $table->foreignId('patient_id')
                ->unique()
                ->constrained('patients')
                ->onDelete('cascade');

            // Demographics & Social
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed', 'separated', 'other'])->nullable();
            $table->integer('number_of_dependents')->nullable();
            $table->enum('living_arrangement', ['alone','with_family','with_partner','shared_housing','care_facility','other'])->nullable();

            // Economic
            $table->enum('employment_status', ['employed_full_time', 'employed_part_time', 'self_employed', 'unemployed', 'retired', 'student', 'unable_to_work', 'other'])->nullable();
            $table->string('occupation')->nullable();
            $table->enum('income_level', ['low','lower_middle','middle','upper_middle','high'])->nullable();
            $table->boolean('has_health_insurance')->default(false);

            // Lifestyle
            $table->enum('education_level', ['no_formal','primary','secondary','vocational','bachelors','masters','doctorate','other'])->nullable();
            $table->enum('smoking_status', ['never', 'former', 'current_light', 'current_heavy'])->nullable();
            $table->enum('alcohol_consumption', ['none', 'occasional', 'moderate', 'heavy'])->nullable();
            $table->enum('physical_activity_level', ['sedentary','lightly_active','moderately_active','very_active'])->nullable();

            // Support Systems
            $table->boolean('has_family_support')->default(false);
            $table->boolean('has_caregiver')->default(false);
            $table->enum('transportation_access', ['own_vehicle','public_transport','rideshare','walking','limited','none'])->nullable();

            // Food Security
            $table->enum('food_security_status', ['food_secure','marginally_secure','food_insecure','severely_insecure'])->nullable();
            $table->text('dietary_restrictions_cultural')->nullable();

            // Additional Notes
            $table->text('additional_notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_socioeconomic');
    }
};
