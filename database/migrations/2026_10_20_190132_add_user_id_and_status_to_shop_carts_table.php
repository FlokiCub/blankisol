<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Corrección de esquema: Alinea shop_carts y shop_cart_items con
 * el ShopAuthController y los tests de la tienda.
 *
 * Cambios:
 *  1. shop_carts: agrega user_id (FK nullable a users), status, coupon_discount_amount.
 *  2. shop_cart_items: renombra price_snapshot → unit_price.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── 1. shop_carts ─────────────────────────────────────────────────────────
        Schema::table('shop_carts', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->nullable()
                ->after('session_id')
                ->constrained('users')
                ->nullOnDelete();

            $table->string('status', 30)->default('active')->after('coupon_code');
            $table->decimal('coupon_discount_amount', 12, 2)->default(0.00)->after('status');
        });

        // ── 2. shop_cart_items: renombrar price_snapshot → unit_price ─────────────
        Schema::table('shop_cart_items', function (Blueprint $table) {
            $table->renameColumn('price_snapshot', 'unit_price');
        });
    }

    public function down(): void
    {
        Schema::table('shop_cart_items', function (Blueprint $table) {
            $table->renameColumn('unit_price', 'price_snapshot');
        });

        Schema::table('shop_carts', function (Blueprint $table) {
            $table->dropColumn(['coupon_discount_amount', 'status']);
            $table->dropConstrainedForeignId('user_id');
        });
    }
};

