<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Corrección de esquema: Alinea shop_customers con los campos usados en
 * registro de cliente (B2C y B2B) y autenticación con Google.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shop_customers', function (Blueprint $table) {
            $table->string('first_name', 100)->nullable()->change();
            $table->string('email')->nullable()->change();

            if (!Schema::hasColumn('shop_customers', 'company_name')) {
                $table->string('company_name', 255)->nullable();
            }

            if (!Schema::hasColumn('shop_customers', 'default_province')) {
                $table->string('default_province', 100)->nullable();
            }

            if (!Schema::hasColumn('shop_customers', 'default_municipality')) {
                $table->string('default_municipality', 100)->nullable();
            }

            if (!Schema::hasColumn('shop_customers', 'is_verified')) {
                $table->boolean('is_verified')->default(true);
            }
        });
    }

    public function down(): void
    {
        Schema::table('shop_customers', function (Blueprint $table) {
            if (Schema::hasColumn('shop_customers', 'is_verified')) {
                $table->dropColumn('is_verified');
            }
            if (Schema::hasColumn('shop_customers', 'default_municipality')) {
                $table->dropColumn('default_municipality');
            }
            if (Schema::hasColumn('shop_customers', 'default_province')) {
                $table->dropColumn('default_province');
            }
            if (Schema::hasColumn('shop_customers', 'company_name')) {
                $table->dropColumn('company_name');
            }
        });
    }
};
