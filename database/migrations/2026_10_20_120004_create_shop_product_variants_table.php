<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shop_product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                ->constrained('shop_products')
                ->cascadeOnDelete();
            $table->string('sku', 60)->unique();
            $table->string('name', 120); // Ej: "128GB / Gris Espacial"
            $table->json('options')->nullable(); // Ej: {"capacidad": "128GB", "color": "Gris"}
            $table->decimal('price', 12, 2)->nullable(); // Sobrescribe precio base si no es nulo
            $table->integer('stock_quantity')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_product_variants');
    }
};
