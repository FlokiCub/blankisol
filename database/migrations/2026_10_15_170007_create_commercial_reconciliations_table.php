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
        Schema::create('commercial_reconciliations', function (Blueprint $table) {
            $table->id();
            $table->string('year_month', 7)->unique(); // YYYY-MM
            $table->decimal('agents_total_amount', 12, 2)->default(0.00); // Declarado por gestores
            $table->decimal('operations_total_amount', 12, 2)->default(0.00); // Registrado en contenedores
            $table->decimal('difference', 12, 2)->default(0.00); // Diferencia
            $table->string('status')->default('pendiente'); // pendiente, concilio_exacto, con_desviacion, aprobado
            $table->text('observations')->nullable();
            $table->foreignId('reconciled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reconciled_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commercial_reconciliations');
    }
};
