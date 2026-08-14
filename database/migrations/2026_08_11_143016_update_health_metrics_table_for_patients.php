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
            $table->dropColumn(['weight', 'blood_pressure']);
            $table->integer('age')->default(0);
            $table->enum('gender', ['Pria', 'Wanita'])->default('Pria');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('health_metrics', function (Blueprint $table) {
            $table->dropColumn(['age', 'gender']);
            $table->integer('weight')->default(0);
            $table->string('blood_pressure')->default('');
        });
    }
};
