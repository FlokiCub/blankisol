<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shop_customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            
            // Modalidad de cliente: B2C ('individual') vs B2B ('business')
            $table->string('customer_type', 20)->default('individual')->index();
            $table->string('first_name', 100);
            $table->string('last_name', 100)->nullable();
            $table->string('email')->index();
            $table->string('phone', 40)->nullable();
            
            // Datos fiscales específicos B2B
            $table->string('business_name', 160)->nullable()->index(); // Razón Social
            $table->string('tax_id', 50)->nullable()->index(); // NIF / CIF / NIT / Registro mercantil
            
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('shop_customer_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')
                ->constrained('shop_customers')
                ->cascadeOnDelete();
            $table->string('address_type', 20)->default('shipping'); // 'shipping' | 'billing'
            $table->string('recipient_name', 140);
            $table->string('phone', 40);
            $table->string('province', 80);
            $table->string('municipality', 80);
            $table->string('address_line1', 200);
            $table->string('address_line2', 200)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_customer_addresses');
        Schema::dropIfExists('shop_customers');
    }
};
