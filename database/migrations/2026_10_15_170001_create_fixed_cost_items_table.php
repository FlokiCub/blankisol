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
        Schema::create('fixed_cost_items', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('cost_center'); // GENERAL, OCC, CEN, ORI
            $table->string('classification'); // Gastos de fuerza de trabajo, Materias primas y materiales, Otros gastos monetarios, Energía, Depreciación y amortización
            $table->boolean('is_salary')->default(false);
            $table->decimal('monthly_base_value', 12, 2)->default(0.00);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fixed_cost_items');
    }
};
