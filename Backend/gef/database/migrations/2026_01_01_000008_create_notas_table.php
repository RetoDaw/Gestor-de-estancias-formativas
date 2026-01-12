<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Sistema de notas
     * Flujo: Competencia → RA → Asignatura
     */
    public function up(): void
    {
        // Notas del centro 
        Schema::create('nota_asignatura_centro', function (Blueprint $table) {
            $table->id('id_nota');
            $table->unsignedBigInteger('id_alumno');
            $table->unsignedBigInteger('id_asignatura');
            $table->decimal('nota', 4, 2)->nullable();
            $table->timestamps();

            $table->unique(['id_alumno', 'id_asignatura'], 'unique_alumno_asig');

            $table->foreign('id_alumno')
                  ->references('id_alumno')
                  ->on('alumno')
                  ->onDelete('cascade');

            $table->foreign('id_asignatura')
                  ->references('id_asignatura')
                  ->on('asignatura')
                  ->onDelete('cascade');
        });

        // Notas de empresa en resultados de aprendizaje
        Schema::create('nota_resultado_aprendizaje', function (Blueprint $table) {
            $table->id('id_nota_ra');
            $table->unsignedBigInteger('id_estancia');
            $table->unsignedBigInteger('id_competencia');
            $table->unsignedBigInteger('id_resultado');
            $table->decimal('nota', 4, 2);
            $table->timestamps();

            $table->unique(['id_estancia', 'id_competencia', 'id_resultado'], 'unique_estancia_ra_competencia');

            $table->foreign('id_estancia')
                  ->references('id_estancia')
                  ->on('estancia')
                  ->onDelete('cascade');

            // Foreign key compuesta
            $table->foreign(['id_competencia', 'id_resultado'])
                  ->references(['id_competencia', 'id_resultado'])
                  ->on('competencia_tecnica_resultado')
                  ->onDelete('cascade');
        });

        // Notas de competencias transversales
        Schema::create('nota_competencia_transversal', function (Blueprint $table) {
            $table->id('id_nota_trans');
            $table->unsignedBigInteger('id_estancia');
            $table->unsignedBigInteger('id_competencia_trans');
            $table->decimal('nota', 4, 2)->nullable();
            $table->timestamps();

            $table->unique(['id_estancia', 'id_competencia_trans'], 'unique_estancia_trans');

            $table->foreign('id_estancia')
                  ->references('id_estancia')
                  ->on('estancia')
                  ->onDelete('cascade');

            $table->foreign('id_competencia_trans')
                  ->references('id_competencia_trans')
                  ->on('competencia_transversal')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nota_competencia_transversal');
        Schema::dropIfExists('nota_resultado_aprendizaje');
        Schema::dropIfExists('nota_asignatura_centro');
    }
};