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
        Schema::create('disaster_predictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('weather_data_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('city', 100);
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->boolean('disaster_predicted');
            $table->decimal('disaster_probability', 5, 4);
            $table->decimal('safe_probability', 5, 4);
            $table->enum('risk_level', ['Low', 'Medium', 'High', 'Critical']);
            $table->string('disaster_type', 50)->nullable();
            $table->json('warnings')->nullable();
            $table->json('input_features')->nullable();
            $table->timestamp('predicted_at');
            $table->timestamps();

            $table->index(['city', 'predicted_at']);
            $table->index(['risk_level', 'predicted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disaster_predictions');
    }
};
