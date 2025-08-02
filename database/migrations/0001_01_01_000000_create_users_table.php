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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('last_name')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->integer('age')->nullable();

            $table->integer('coins')->default(0);
            $table->boolean('share')->default(0);
            $table->json('categories')->nullable();

            $table->string('password');
            $table->enum('role', ['user', 'admin'])->default('user');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('avatar')->nullable();
            $table->string('phone')->nullable();
            $table->string('otp')->nullable();
            $table->timestamp('otp_expiration')->nullable();
            $table->string('reset_token')->nullable();
            $table->datetime('last_login_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
