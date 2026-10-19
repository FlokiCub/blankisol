<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('commercial_agents', function (Blueprint $table) {
            $table->id();
            $table->integer('agent_number')->nullable()->unique();
            $table->string('name');
            $table->string('group'); // Grupo central, Provincial
            $table->string('territory'); // Oficina central, Pinar del Río, Artemisa, etc.
            $table->string('region'); // General, Occidente, Centro, Oriente
            $table->decimal('monthly_fixed_fee', 10, 2)->default(50.00); // Pago fijo mensual USD
            $table->string('commission_mode')->default('% DEL INGRESO'); // % DEL INGRESO, USD POR KG
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commercial_agents');
    }
};
