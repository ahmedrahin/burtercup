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
        Schema::create('wishlist_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('charity_wishlist_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->integer('coin')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wishlist_lists');
    }
};
