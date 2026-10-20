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
        Schema::create('tech_service_requests', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->string('empresa', 150)->nullable();
            $table->string('email', 150);
            $table->string('telefono', 50);
            $table->string('servicio_interes', 200);
            $table->string('tipo_instalacion', 200)->nullable();
            $table->string('ubicacion', 255)->nullable();
            $table->string('superficie_volumen', 100)->nullable();
            $table->string('nivel_complejidad', 50)->nullable();
            $table->text('descripcion');
            $table->string('plazo_deseado', 100)->nullable();
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
        Schema::dropIfExists('tech_service_requests');
    }
};
