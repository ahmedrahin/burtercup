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

            $table->unsignedBigInteger('brand_id')->nullable();
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('set null');

            $table->unsignedBigInteger('category_id')->nullable();
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');

            $table->integer('subcategory_id')->nullable();

            $table->text('description')->nullable();
            $table->text('long_description')->nullable();

            $table->decimal('base_price', 8, 2)->default(0);
            $table->unsignedTinyInteger('discount_option')->nullable()->comment('1=No Discount, 2=Percentage %, 3=Fixed Price');
            $table->decimal('discount_percentage_or_flat_amount', 5, 2)->nullable();
            $table->decimal('discount_amount', 8, 2)->nullable();
            $table->decimal('offer_price', 8, 2)->default(0);

            $table->unsignedInteger('quantity')->default(0);
            $table->string('sku_code')->nullable();

            $table->unsignedTinyInteger('status')->default(1)->comment('0=Inactive, 1=Active, 2=Draft, 3=Scheduled');
            $table->timestamp('publish_at')->nullable()->comment('Scheduled publish date');
            $table->timestamp('expire_date')->nullable()->comment('Scheduled expire date');

            $table->string('thumb_image')->nullable();
            $table->string('back_image')->nullable();
            $table->string('model')->nullable();
            $table->string('gender')->nullable();

            $table->enum('free_shipping', ['yes', 'no'])->default('no')->comment('yes or no');
            $table->boolean('is_new')->default(false)->comment('0=No, 1=Yes');
            $table->boolean('is_featured')->default(false)->comment('0=No, 1=Yes');

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
