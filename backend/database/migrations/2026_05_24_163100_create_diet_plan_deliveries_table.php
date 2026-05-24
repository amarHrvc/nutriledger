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
        Schema::create('diet_plan_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diet_plan_id')->constrained('patient_diet_plans')->cascadeOnDelete();
            $table->foreignId('sent_by')->constrained('users')->restrictOnDelete();
            $table->string('recipient_email', 255);
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->string('failure_reason', 500)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diet_plan_deliveries');
    }
};
