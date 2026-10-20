<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shop_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 40)->unique();
            $table->foreignId('customer_id')
                ->nullable()
                ->constrained('shop_customers')
                ->nullOnDelete();
            
            // Tipo de cliente y snapshots inmutables
            $table->string('customer_type', 20)->default('individual')->index();
            $table->json('customer_data'); // Snapshot con nombre, email, teléfono, razón social, tax_id
            $table->json('shipping_address'); // Snapshot con dirección completa de entrega
            $table->json('billing_address')->nullable();
            
            // Estados del ciclo comercial
            // status: 'pending', 'paid', 'processing', 'shipped', 'delivered', 'cancelled'
            $table->string('status', 30)->default('pending')->index();
            // payment_status: 'unpaid', 'paid', 'failed', 'refunded'
            $table->string('payment_status', 30)->default('unpaid')->index();
            
            // Desglose financiero en USD (decimal para máxima precisión)
            $table->string('currency', 10)->default('USD');
            $table->decimal('subtotal', 12, 2);
            $table->decimal('discount_amount', 12, 2)->default(0.00);
            $table->string('coupon_code', 40)->nullable();
            
            // Envío
            $table->foreignId('shipping_rate_id')
                ->nullable()
                ->constrained('shop_shipping_rates')
                ->nullOnDelete();
            $table->decimal('shipping_fee', 12, 2)->default(0.00);
            $table->decimal('tax_amount', 12, 2)->default(0.00);
            $table->decimal('total', 12, 2);
            
            // Notas y metadatos
            $table->text('notes')->nullable();
            $table->string('idempotency_key', 80)->nullable()->unique();
            
            // Fechas de hitos
            $table->timestamp('placed_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('shop_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                ->constrained('shop_orders')
                ->cascadeOnDelete();
            $table->foreignId('product_id')
                ->nullable()
                ->constrained('shop_products')
                ->nullOnDelete();
            $table->foreignId('variant_id')
                ->nullable()
                ->constrained('shop_product_variants')
                ->nullOnDelete();
            
            // Snapshot inmutable de producto comprado
            $table->string('sku', 60);
            $table->string('product_name', 200);
            $table->string('variant_name', 120)->nullable();
            $table->string('inventory_type', 30)->default('controlled_stock');
            $table->integer('quantity');
            $table->decimal('unit_price', 12, 2);
            $table->decimal('total_price', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_order_items');
        Schema::dropIfExists('shop_orders');
    }
};
