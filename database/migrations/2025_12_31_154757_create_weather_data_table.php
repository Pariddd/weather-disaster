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
        Schema::create('weather_data', function (Blueprint $table) {
            $table->id();
            $table->string('city', 100);
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->decimal('temperature', 5, 2);
            $table->decimal('feels_like', 5, 2)->nullable();
            $table->integer('humidity');
            $table->integer('pressure');
            $table->decimal('wind_speed', 5, 2);
            $table->integer('wind_deg')->nullable();
            $table->decimal('rainfall', 8, 2)->default(0);
            $table->integer('clouds')->nullable();
            $table->string('weather_main', 50)->nullable();
            $table->string('weather_description', 100)->nullable();
            $table->string('weather_icon', 10)->nullable();
            $table->timestamp('recorded_at');
            $table->timestamps();

            $table->index(['city', 'recorded_at']);
            $table->index('recorded_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weather_data');
    }
};
