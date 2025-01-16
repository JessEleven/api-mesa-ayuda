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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_ticket', 20)->unique();
            $table->text('descripcion');
            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_fin');
            $table->timestamps();

            $table->foreignId('id_categoria')
            ->constrained('categorias_tickets')
            ->cascadeOnDelete()
            ->cascadeOnUpdate();
            $table->foreignId('id_usuario')
            ->constrained('usuarios')
            ->cascadeOnDelete()
            ->cascadeOnUpdate();
            $table->foreignId('id_estado')
            ->constrained('estados_tickets')
            ->cascadeOnDelete()
            ->cascadeOnUpdate();
            $table->foreignId('id_prioridad')
            ->constrained('prioridades_tickets')
            ->cascadeOnDelete()
            ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
