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
        Schema::create('departamentos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_departamento')->unique();
            $table->string('sigla_departamento')->unique();
            $table->string('secuencia_departamento', 20);
            $table->integer('peso_prioridad')->unique();
            $table->timestamps();

            $table->foreignId('id_area')
            ->constrained('areas')
            ->cascadeOnDelete()
            ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departamentos');
    }
};
