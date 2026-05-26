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
        Schema::table('patient_diet_plans', function (Blueprint $table) {
            $table->boolean('is_edited')->default(false)->after('status');
            $table->foreignId('edited_by')->nullable()->constrained('users')->nullOnDelete()->after('is_edited');
            $table->timestamp('edited_at')->nullable()->after('edited_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patient_diet_plans', function (Blueprint $table) {
            $table->dropForeignIdFor('edited_by');
            $table->dropColumn(['is_edited', 'edited_by', 'edited_at']);
        });
    }
};
