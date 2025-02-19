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
        Schema::create('tecnicos_asignados', function (Blueprint $table) {
            $table->id();
            $table->timestamp('recurso_eliminado')->nullable();
            $table->timestamps();

            $table->foreignId('id_usuario')
            ->constrained('usuarios')
            ->cascadeOnDelete()
            ->cascadeOnUpdate();
            $table->foreignId('id_ticket')
            ->nullable()
            ->constrained('tickets')
            ->cascadeOnDelete()
            ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tecnicos_asignados');
    }
};
