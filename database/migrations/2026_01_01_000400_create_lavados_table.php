<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cada vez que un vehículo entra al lavadero se crea un "lavado"
        Schema::create('lavados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehiculo_id')->constrained('vehiculos');
            $table->foreignId('cliente_id')->constrained('clientes');
            $table->foreignId('empleado_id')->nullable()->constrained('users')->nullOnDelete(); // quién lo lava
            $table->foreignId('registrado_por')->constrained('users');                       // quién lo registró
            $table->string('estado', 20)->default('pendiente');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('descuento', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->boolean('es_gratis')->default(false);
            $table->boolean('pagado')->default(false);
            $table->string('metodo_pago', 20)->nullable();
            $table->timestamp('pagado_en')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['created_at', 'estado']);
        });

        // Servicios que se le hicieron en ese lavado (guardamos el precio de ese momento)
        Schema::create('lavado_servicio', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lavado_id')->constrained('lavados')->cascadeOnDelete();
            $table->foreignId('servicio_id')->constrained('servicios');
            $table->decimal('precio', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lavado_servicio');
        Schema::dropIfExists('lavados');
    }
};
