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
        Schema::create('patient_diet_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('generated_by')->constrained('users')->restrictOnDelete();
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
            $table->text('rationale')->nullable();
            $table->integer('daily_calories')->nullable();
            $table->json('nutritional_goals')->nullable();
            $table->json('days')->nullable();
            $table->json('warnings')->nullable();
            $table->string('failure_reason', 500)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_diet_plans');
    }
};
