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
        Schema::table('health_metrics', function (Blueprint $table) {
            $table->dropColumn('age');
            $table->enum('age_group', ['30-40', '41-50', '51-60', '60+'])->default('30-40');
            $table->integer('patient_count')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('health_metrics', function (Blueprint $table) {
            $table->dropColumn(['age_group', 'patient_count']);
            $table->integer('age')->default(0);
        });
    }
};
