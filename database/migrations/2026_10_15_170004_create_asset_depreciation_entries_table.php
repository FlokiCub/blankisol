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
        Schema::create('asset_depreciation_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->string('year_month', 7)->index(); // YYYY-MM
            $table->decimal('monthly_depreciation', 12, 2); // Depreciación mensual en USD
            $table->decimal('accumulated_depreciation', 12, 2); // Depreciación acumulada en USD
            $table->unique(['asset_id', 'year_month']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_depreciation_entries');
    }
};
