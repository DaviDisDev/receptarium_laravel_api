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
        Schema::create('pasos_recetas', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->text('descripcion');
            $table->text('ruta_imagen')->nullable(); // Permite que ruta_imagen sea nullable
            $table->text('tiempo_paso')->nullable(); // Permite que tiempo_paso sea nullable
            $table->unsignedBigInteger('receta_id');
            $table->foreign('receta_id')->references('id')->on('recetas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pasos_recetas');
    }
};
