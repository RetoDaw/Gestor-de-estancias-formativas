<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Competencias técnicas y su relación N:N con resultados de aprendizaje
     */
    public function up(): void
    {
        // Competencias técnicas
        Schema::create('competencia_tecnica', function (Blueprint $table) {
            $table->id('id_competencia');
            $table->unsignedBigInteger('id_grado');
            $table->text('descripcion');
            $table->timestamps();

            $table->foreign('id_grado')
                  ->references('id_grado')
                  ->on('grado')
                  ->onDelete('cascade');
        });

        // Tabla pivot: relación N:N entre competencias y resultados
        Schema::create('competencia_tecnica_resultado', function (Blueprint $table) {
            $table->unsignedBigInteger('id_competencia');
            $table->unsignedBigInteger('id_resultado');
            $table->timestamps();

            $table->primary(['id_competencia', 'id_resultado']);

            $table->foreign('id_competencia')
                  ->references('id_competencia')
                  ->on('competencia_tecnica')
                  ->onDelete('cascade');

            $table->foreign('id_resultado')
                  ->references('id_resultado')
                  ->on('resultado_aprendizaje')
                  ->onDelete('cascade');
        });

        // Competencias transversales (4 fijas)
        Schema::create('competencia_transversal', function (Blueprint $table) {
            $table->id('id_competencia_trans');
            $table->text('descripcion');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competencia_transversal');
        Schema::dropIfExists('competencia_tecnica_resultado');
        Schema::dropIfExists('competencia_tecnica');
    }
};