<?php

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
        Schema::create('economic_parameters', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('value'); // Numeric string or text parameter
            $table->string('unit');
            $table->string('category')->default('general'); // puerto, transitaria, distribucion, impuestos, general
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('fuel_and_tariff_rates', function (Blueprint $table) {
            $table->id();
            $table->date('effective_from');
            $table->decimal('tariff_occidente_usd_kg', 8, 4)->default(0.8000);
            $table->decimal('tariff_centro_usd_kg', 8, 4)->default(0.8000);
            $table->decimal('tariff_oriente_usd_kg', 8, 4)->default(0.8000);
            $table->decimal('diesel_price_usd_l', 8, 4)->default(2.0000);
            $table->decimal('port_appointment_usd', 8, 2)->default(10.00);
            $table->decimal('driver_diet_usd', 8, 2)->default(10.00);
            $table->decimal('labor_shift_transitaria_usd', 8, 2)->default(15.00);
            $table->decimal('labor_shift_transitory_center_usd', 8, 2)->default(15.00);
            $table->decimal('transitaria_service_enci_usd', 8, 2)->default(0.00);
            $table->decimal('transitaria_service_av_usd', 8, 2)->default(0.00);
            $table->decimal('commercial_commission_pct', 6, 4)->default(0.0300);
            $table->decimal('commercial_commission_usd_kg', 8, 4)->default(0.0300);
            $table->decimal('vehicle_wear_usd_km', 8, 4)->default(0.1200);
            $table->string('support_document_note')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('fixed_costs_monthly', function (Blueprint $table) {
            $table->id();
            $table->string('year_month', 7)->unique(); // YYYY-MM
            $table->decimal('general_fixed_usd', 10, 2)->default(2639.81);
            $table->decimal('occidente_fixed_usd', 10, 2)->default(200.00);
            $table->decimal('centro_fixed_usd', 10, 2)->default(200.00);
            $table->decimal('oriente_fixed_usd', 10, 2)->default(200.00);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('container_profitability_analyses', function (Blueprint $table) {
            $table->id();
            $table->string('container_id')->index();
            $table->date('arrival_date');
            $table->string('transitaria')->default('ENCI');
            $table->string('status')->default('Cerrado'); // Cerrado, En Proceso, Estimado

            // Weights
            $table->decimal('kg_occidente', 10, 2)->default(0);
            $table->decimal('kg_centro', 10, 2)->default(0);
            $table->decimal('kg_oriente', 10, 2)->default(0);
            $table->decimal('kg_total', 10, 2)->default(0);

            // Revenues
            $table->decimal('revenue_occidente', 10, 2)->default(0);
            $table->decimal('revenue_centro', 10, 2)->default(0);
            $table->decimal('revenue_oriente', 10, 2)->default(0);
            $table->decimal('revenue_other', 10, 2)->default(0);
            $table->decimal('revenue_total', 10, 2)->default(0);

            // Costs
            $table->decimal('cost_port_total', 10, 2)->default(0);
            $table->decimal('cost_transitaria_total', 10, 2)->default(0);
            $table->decimal('cost_distribution_occidente', 10, 2)->default(0);
            $table->decimal('cost_distribution_centro', 10, 2)->default(0);
            $table->decimal('cost_distribution_oriente', 10, 2)->default(0);
            $table->decimal('cost_distribution_total', 10, 2)->default(0);
            $table->decimal('cost_commission_total', 10, 2)->default(0);
            $table->decimal('cost_provisions_and_bank', 10, 2)->default(0);
            $table->decimal('cost_variable_total', 10, 2)->default(0);

            // Margins & Fixed Costs
            $table->decimal('contribution_margin', 10, 2)->default(0);
            $table->decimal('contribution_margin_pct', 6, 2)->default(0);
            $table->decimal('fixed_costs_assigned_total', 10, 2)->default(0);

            // Taxes & Net Results
            $table->decimal('taxes_sales_and_services', 10, 2)->default(0);
            $table->decimal('tax_territorial_contribution', 10, 2)->default(0);
            $table->decimal('profit_before_tax', 10, 2)->default(0);
            $table->decimal('estimated_income_tax', 10, 2)->default(0);
            $table->decimal('net_profit', 10, 2)->default(0);
            $table->decimal('net_margin_pct', 6, 2)->default(0);

            // Metrics & Decision Verdict
            $table->decimal('cost_per_kg', 8, 4)->default(0);
            $table->decimal('revenue_per_kg', 8, 4)->default(0);
            $table->decimal('profit_before_tax_per_kg', 8, 4)->default(0);
            $table->decimal('breakeven_kg', 10, 2)->default(0);
            $table->decimal('breakeven_tariff_usd_kg', 8, 4)->default(0);
            $table->string('decision_verdict')->default('FACTIBLE'); // FACTIBLE, MARGINAL, NO_FACTIBLE
            $table->text('decision_summary')->nullable();
            $table->jsonb('regional_breakdown')->nullable();
            $table->jsonb('applied_parameters')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('container_profitability_analyses');
        Schema::dropIfExists('fixed_costs_monthly');
        Schema::dropIfExists('fuel_and_tariff_rates');
        Schema::dropIfExists('economic_parameters');
    }
};
