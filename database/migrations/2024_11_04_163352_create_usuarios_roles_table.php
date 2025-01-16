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
        Schema::create('usuarios_roles', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignId('id_rol')
            ->constrained('roles')
            ->cascadeOnDelete()
            ->cascadeOnUpdate();
            $table->foreignId('id_usuario')
            ->constrained('usuarios')
            ->cascadeOnDelete()
            ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios_roles');
    }
};
