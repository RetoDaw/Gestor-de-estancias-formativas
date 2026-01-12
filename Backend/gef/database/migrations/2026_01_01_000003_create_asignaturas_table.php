<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Estructura académica: GRADO → ASIGNATURA → RESULTADO DE APRENDIZAJE
     */
    public function up(): void
    {
        // Tabla de asignaturas
        Schema::create('asignatura', function (Blueprint $table) {
            $table->id('id_asignatura');
            $table->unsignedBigInteger('id_grado');
            $table->string('nombre', 150);
            $table->timestamps();

            $table->foreign('id_grado')
                  ->references('id_grado')
                  ->on('grado')
                  ->onDelete('cascade');
        });

        // Tabla de resultados de aprendizaje
        Schema::create('resultado_aprendizaje', function (Blueprint $table) {
            $table->id('id_resultado');
            $table->unsignedBigInteger('id_asignatura');
            $table->text('descripcion');
            $table->timestamps();

            $table->foreign('id_asignatura')
                  ->references('id_asignatura')
                  ->on('asignatura')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resultado_aprendizaje');
        Schema::dropIfExists('asignatura');
    }
};