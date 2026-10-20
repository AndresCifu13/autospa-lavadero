<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Catálogo de servicios con un precio para cada tipo de vehículo (HU-07)
        Schema::create('servicios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->decimal('precio_auto', 12, 2)->default(0);
            $table->decimal('precio_camioneta', 12, 2)->default(0);
            $table->decimal('precio_moto', 12, 2)->default(0);
            $table->unsignedInteger('duracion_minutos')->default(30);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servicios');
    }
};
