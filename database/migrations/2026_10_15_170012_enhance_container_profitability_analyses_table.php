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
        Schema::table('container_profitability_analyses', function (Blueprint $table) {
            $table->string('economic_status')->default('abierto')->after('status'); // abierto, en_revision, cerrado
            $table->foreignId('closed_by')->nullable()->after('economic_status')->constrained('users')->nullOnDelete();
            $table->timestamp('closed_at')->nullable()->after('closed_by');
            $table->text('reopened_reason')->nullable()->after('closed_at');
            $table->jsonb('applied_rates_snapshot')->nullable()->after('reopened_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('container_profitability_analyses', function (Blueprint $table) {
            $table->dropForeign(['closed_by']);
            $table->dropColumn([
                'economic_status',
                'closed_by',
                'closed_at',
                'reopened_reason',
                'applied_rates_snapshot',
            ]);
        });
    }
};
