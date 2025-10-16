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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->onDelete('cascade');
            $table->string('first_name');
            $table->string('last_name');
            $table->date('date_of_birth');
            $table->enum("gender", ["M", "F"]);
            $table->string("phone");
            $table->string("address")->nullable(true);
            $table->string("city")->nullable(true);
            $table->string("postal_code")->nullable(true);
            $table->string("emergency_contact_name");
            $table->string("emergency_contact_phone");
            $table->enum("blood_type", ["A+", "A-", "B+", "B-", "AB+", "AB-", "O+", "O-"])->nullable(true);
            $table->text("allergies")->nullable(true);
            $table->text("medical_notes")->nullable(true);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
