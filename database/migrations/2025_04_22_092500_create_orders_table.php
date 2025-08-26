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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('order_id')->unique();
            $table->string('user_type')->comment('Author and Customer')->default('customer');
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('shipping_address');
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('zip_code')->nullable();
            $table->double('grand_total', 20, 2)->nullable();
            $table->double('subtotal', 20, 2)->nullable();
            $table->integer('paid_amount')->nullable();
            $table->integer('discount_amount')->nullable();
            $table->integer('delivery_option_id')->nullable();
            $table->integer('delivery_option_price')->nullable();
            $table->integer('shipping_method')->nullable();
            $table->double('shipping_cost', 8, 2)->default(0);
            $table->string('shipping_type', 50)->nullable();
            $table->text('cupon_code')->nullable();
            $table->double('coupon_discount', 20, 2)->nullable();
            $table->string('delivery_status', 20)->default('pending');
            $table->string('payment_method')->nullable();
            $table->string('payment_from', 191)->nullable();
            $table->longText('note')->nullable();
            $table->string('order_source', 15)->nullable();
            $table->string('transaction_id')->nullable();
            $table->timestamp('order_date')->nullable();
            $table->timestamp('order_status_date')->nullable();
            $table->boolean('viewed')->default(0);
            $table->boolean('is_seen')->default(0);
            $table->unsignedBigInteger('admin_id_to_seen_order')->nullable();
            $table->unsignedBigInteger('admin_id_to_view_order')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
