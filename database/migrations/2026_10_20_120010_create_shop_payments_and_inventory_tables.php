<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shop_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                ->constrained('shop_orders')
                ->cascadeOnDelete();
            $table->string('gateway', 40); // 'stripe', 'paypal', 'mock_sandbox', etc.
            $table->string('transaction_id', 120)->nullable()->index();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 10)->default('USD');
            // status: 'pending', 'approved', 'declined', 'cancelled', 'refunded'
            $table->string('status', 30)->default('pending')->index();
            $table->string('idempotency_key', 80)->nullable()->unique();
            $table->json('payload')->nullable(); // Metadata o respuesta encriptada de la pasarela
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });

        Schema::create('shop_inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                ->constrained('shop_products')
                ->cascadeOnDelete();
            $table->foreignId('variant_id')
                ->nullable()
                ->constrained('shop_product_variants')
                ->nullOnDelete();
            $table->foreignId('order_id')
                ->nullable()
                ->constrained('shop_orders')
                ->nullOnDelete();
            // movement_type: 'purchase_out', 'manual_adjustment', 'stock_in', 'reservation', 'reservation_release'
            $table->string('movement_type', 40)->index();
            $table->integer('quantity'); // Positivo o negativo
            $table->integer('stock_before');
            $table->integer('stock_after');
            $table->string('reason')->nullable();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('created_at')->useCurrent()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_inventory_movements');
        Schema::dropIfExists('shop_payments');
    }
};
