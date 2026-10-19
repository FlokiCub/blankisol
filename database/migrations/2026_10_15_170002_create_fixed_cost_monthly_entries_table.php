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
        Schema::create('fixed_cost_monthly_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fixed_cost_item_id')->constrained('fixed_cost_items')->cascadeOnDelete();
            $table->string('year_month', 7)->index(); // YYYY-MM
            $table->decimal('actual_amount', 12, 2);
            $table->text('observations')->nullable();
            $table->jsonb('documents')->nullable();
            $table->unique(['fixed_cost_item_id', 'year_month']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fixed_cost_monthly_entries');
    }
};
