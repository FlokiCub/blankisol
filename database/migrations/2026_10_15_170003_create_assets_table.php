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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_number')->nullable(); // Chapa o código de inventario
            $table->string('description'); // Descripción del activo
            $table->string('asset_type'); // Vehículo propio, Equipo, etc.
            $table->date('registration_date'); // Fecha de alta
            $table->decimal('acquisition_value', 12, 2); // Valor de adquisición (USD)
            $table->decimal('residual_value', 12, 2)->default(0.00); // Valor residual (USD)
            $table->integer('useful_life_years'); // Vida útil en años
            $table->date('deregistration_date')->nullable(); // Fecha de baja si aplica
            $table->decimal('monthly_maintenance', 12, 2)->default(0.00); // Mantenimiento preventivo (USD/mes)
            $table->decimal('annual_insurance', 12, 2)->default(0.00); // Seguros y licencias (USD/año)
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
