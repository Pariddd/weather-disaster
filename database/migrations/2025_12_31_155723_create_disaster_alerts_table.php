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
        Schema::create('disaster_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disaster_prediction_id')->constrained()->onDelete('cascade');
            $table->string('city', 100);
            $table->enum('alert_level', ['Warning', 'Alert', 'Emergency']);
            $table->string('title', 255);
            $table->text('message');
            $table->boolean('is_active')->default(true);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['city', 'is_active']);
            $table->index('alert_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disaster_alerts');
    }
};
