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
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed', 'partnered'])->nullable();
            $table->integer('number_of_dependents')->nullable();
            $table->enum('living_arrangement', ['alone', 'with_family', 'with_partner', 'shared', 'institution'])->nullable();

            // Economic
            $table->enum('employment_status', ['employed_full_time', 'employed_part_time', 'self_employed', 'unemployed', 'retired', 'student', 'disabled'])->nullable();
            $table->string('occupation')->nullable();
            $table->enum('income_level', ['low', 'middle', 'high', 'prefer_not_to_say'])->nullable();
            $table->boolean('has_health_insurance')->default(false);

            // Lifestyle
            $table->enum('education_level', ['primary', 'secondary', 'vocational', 'bachelor', 'master', 'doctorate', 'other'])->nullable();
            $table->enum('smoking_status', ['never', 'former', 'current'])->nullable();
            $table->enum('alcohol_consumption', ['none', 'occasional', 'moderate', 'heavy'])->nullable();
            $table->enum('physical_activity_level', ['sedentary', 'light', 'moderate', 'active', 'very_active'])->nullable();

            // Support Systems
            $table->boolean('has_family_support')->default(false);
            $table->boolean('has_caregiver')->default(false);
            $table->enum('transportation_access', ['own_vehicle', 'public_transport', 'family', 'limited', 'none'])->nullable();

            // Food Security
            $table->enum('food_security_status', ['secure', 'at_risk', 'insecure'])->nullable();
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
