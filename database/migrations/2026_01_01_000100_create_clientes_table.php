<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('documento', 20)->nullable()->unique();
            $table->string('telefono', 20)->nullable();
            $table->string('email')->nullable();
            // Tarjeta de fidelización: cuántas lavadas pagadas lleva hacia la gratis
            $table->unsignedInteger('lavadas_acumuladas')->default(0);
            // Lavadas gratis que ya ganó y todavía no ha usado
            $table->unsignedInteger('lavadas_gratis')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
