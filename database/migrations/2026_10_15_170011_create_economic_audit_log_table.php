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
        Schema::create('economic_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type')->index(); // Tipo de modelo / entidad
            $table->string('entity_id')->index(); // Identificador
            $table->string('action'); // crear, modificar, cerrar, reabrir, simular
            $table->jsonb('old_values')->nullable(); // Valores antes de la mutación
            $table->jsonb('new_values')->nullable(); // Nuevos valores
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('economic_audit_logs');
    }
};
