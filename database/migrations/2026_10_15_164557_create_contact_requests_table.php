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
        Schema::create('contact_requests', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->string('empresa', 150)->nullable();
            $table->string('email', 150);
            $table->string('telefono', 50)->nullable();
            $table->string('modalidad', 50)->default('maritimo');
            $table->string('origen', 150)->nullable();
            $table->string('destino', 150)->nullable();
            $table->string('mercancia', 200)->nullable();
            $table->text('descripcion');
            $table->boolean('aceptado_privacidad')->default(true);
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_requests');
    }
};
