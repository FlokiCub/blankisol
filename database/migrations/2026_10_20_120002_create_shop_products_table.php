<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shop_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')
                ->nullable()
                ->constrained('shop_categories')
                ->nullOnDelete();
            $table->string('sku', 60)->unique();
            $table->string('slug', 160)->unique();
            $table->string('name', 200);
            $table->text('short_description')->nullable();
            $table->text('description')->nullable();
            
            // Precios en USD (Decimal para máxima precisión financiera)
            $table->decimal('price', 12, 2)->default(0.00);
            $table->decimal('sale_price', 12, 2)->nullable();
            $table->decimal('cost_price', 12, 2)->nullable();
            
            // Modalidad de inventario mixto: 'controlled_stock' | 'on_demand'
            $table->string('inventory_type', 30)->default('controlled_stock')->index();
            $table->integer('stock_quantity')->default(0);
            $table->integer('min_stock_alert')->default(5);
            $table->string('estimated_delivery_days', 60)->nullable(); // Ej: "15 a 21 días" para bajo pedido
            
            // Atributos físicos para envíos
            $table->decimal('weight_kg', 8, 2)->nullable();
            $table->string('dimensions', 80)->nullable();
            
            // Flags de estado y vitrina
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_featured')->default(false)->index();
            
            // SEO
            $table->string('seo_title', 160)->nullable();
            $table->text('seo_description')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_products');
    }
};
