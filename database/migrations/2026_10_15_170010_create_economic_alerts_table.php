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
        Schema::create('economic_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('container_profitability_analysis_id')->constrained('container_profitability_analyses')->cascadeOnDelete();
            $table->string('alert_type'); // combustible_desvio, margen_negativo, perdida_regional, sobrecosto_operativo
            $table->string('severity')->default('media'); // baja, media, alta, critica
            $table->text('description'); // Detalle explicativo de la desviación
            $table->boolean('is_investigated')->default(false); // Flag de análisis del jefe económico
            $table->text('justification')->nullable(); // Justificación documentada
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('economic_alerts');
    }
};
