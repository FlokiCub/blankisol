<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shop_coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 40)->unique();
            $table->string('name', 120);
            $table->string('discount_type', 20)->default('percentage'); // 'percentage' | 'fixed' (en USD)
            $table->decimal('discount_value', 12, 2);
            $table->decimal('min_order_amount', 12, 2)->nullable(); // Mínimo de compra en USD
            $table->decimal('max_discount_amount', 12, 2)->nullable(); // Tope máximo de rebaja
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->integer('usage_limit')->nullable(); // Límite total de canjes
            $table->integer('used_count')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_coupons');
    }
};
