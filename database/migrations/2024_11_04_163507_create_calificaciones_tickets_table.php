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
        Schema::create('calificaciones_tickets', function (Blueprint $table) {
            $table->id();
            $table->integer('calificacion');
            $table->text('observacion')->nullable();
            $table->timestamps();

            $table->foreignId('id_ticket')
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
        Schema::dropIfExists('calificaciones_tickets');
    }
};
