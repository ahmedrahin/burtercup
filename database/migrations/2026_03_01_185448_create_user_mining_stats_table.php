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
        Schema::create('user_mining_stats', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Bars
            $table->unsignedTinyInteger('fire_bar')->default(3);
            $table->unsignedTinyInteger('pressure_bar')->default(3);
            $table->unsignedTinyInteger('minerals_bar')->default(0);

            // Mining Progress
            $table->unsignedBigInteger('mining_seconds')->default(0);

            // timestamps tracking
            $table->timestamp('last_mining_calculated_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_mining_stats');
    }
};
