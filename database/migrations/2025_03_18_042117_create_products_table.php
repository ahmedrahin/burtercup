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
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');

            $table->string('name')->unique();
            $table->string('slug')->unique();

            $table->string('category')->nullable();
            $table->string('subcategory')->nullable();

            $table->integer('category_id')->nullable();
             $table->integer('subcategory_id')->nullable();

            $table->text('description')->nullable();
            $table->text('long_description')->nullable();

            $table->integer('coin')->default(0);

            $table->unsignedInteger('quantity')->default(0);

            $table->unsignedTinyInteger('status')->default(1)->comment('0=Inactive, 1=Active, 2=Draft, 3=Scheduled');
             $table->timestamp('expire_date')->nullable()->comment('Scheduled expire date');

            $table->string('thumb_image')->nullable();
            $table->string('back_image')->nullable();
            $table->string('condition')->nullable();

            $table->enum('free_shipping', ['yes', 'no'])->default('no')->comment('yes or no');
            $table->boolean('is_new')->default(false)->comment('0=No, 1=Yes,');
            $table->boolean('is_featured')->default(false)->comment('0=No, 1=Yes');
            $table->string('add_source');

            $table->softDeletes();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
