<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shop_carts', function (Blueprint $table) {
            $table->id();
            $table->string('session_id', 120)->nullable()->index();
            $table->foreignId('customer_id')
                ->nullable()
                ->constrained('shop_customers')
                ->nullOnDelete();
            $table->string('coupon_code', 40)->nullable();
            $table->timestamp('last_activity_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('shop_cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')
                ->constrained('shop_carts')
                ->cascadeOnDelete();
            $table->foreignId('product_id')
                ->constrained('shop_products')
                ->cascadeOnDelete();
            $table->foreignId('variant_id')
                ->nullable()
                ->constrained('shop_product_variants')
                ->nullOnDelete();
            $table->integer('quantity')->default(1);
            $table->decimal('price_snapshot', 12, 2); // Captura del precio unitario en USD
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_cart_items');
        Schema::dropIfExists('shop_carts');
    }
};
