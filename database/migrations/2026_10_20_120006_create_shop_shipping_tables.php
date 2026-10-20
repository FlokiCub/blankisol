<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shop_shipping_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120); // Ej: "La Habana y Mayabeque", "Occidente", "Centro", "Oriente"
            $table->json('provinces')->nullable(); // Array de provincias cubiertas
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('shop_shipping_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zone_id')
                ->constrained('shop_shipping_zones')
                ->cascadeOnDelete();
            $table->string('name', 120); // Ej: "Entrega a Domicilio Estándar", "Express Mariel"
            $table->decimal('price', 12, 2)->default(0.00); // Tarifa en USD
            $table->string('estimated_days', 60)->nullable(); // Ej: "24 a 48 horas"
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_shipping_rates');
        Schema::dropIfExists('shop_shipping_zones');
    }
};
