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
        Schema::create('commercial_liquidations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commercial_agent_id')->constrained('commercial_agents')->cascadeOnDelete();
            $table->string('year_month', 7)->index(); // YYYY-MM
            $table->decimal('monthly_fixed_fee', 10, 2)->default(0.00); // Pago fijo del mes
            $table->decimal('variable_base', 12, 2)->default(0.00); // Base variable (USD cobrados o kg)
            $table->decimal('variable_rate', 6, 4)->default(0.0000); // Tasa aplicada (% o $/kg)
            $table->decimal('calculated_commission', 12, 2)->default(0.00); // Pago variable comisión
            $table->decimal('total_payable', 12, 2)->default(0.00); // TOTAL A PAGAR (fijo + variable)
            $table->string('status')->default('borrador'); // borrador, aprobado, liquidado
            $table->text('notes')->nullable();
            $table->unique(['commercial_agent_id', 'year_month']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commercial_liquidations');
    }
};
