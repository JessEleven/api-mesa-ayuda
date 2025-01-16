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
        Schema::create('bitacora_tickets', function (Blueprint $table) {
            $table->id();
            $table->text('descripcion')->nullable();
            $table->timestamp('recurso_eliminado')->nullable();
            $table->timestamps();

            $table->foreignId('id_tecnico_asignado')
            ->constrained('tecnico_asignados')
            ->cascadeOnDelete()
            ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bitacora_tickets');
    }
};
