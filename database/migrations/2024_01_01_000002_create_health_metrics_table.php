<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('health_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('weight'); // in kg
            $table->string('blood_pressure'); // e.g. "120/80"
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('health_metrics');
    }
};
