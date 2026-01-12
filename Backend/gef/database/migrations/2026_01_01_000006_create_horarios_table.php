<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Sistema de horarios semanales
     */
    public function up(): void
    {
        // Horario semanal (días de la semana)
        Schema::create('horario_semanal', function (Blueprint $table) {
            $table->id('id_horario_semanal');
            $table->unsignedBigInteger('id_estancia');
            $table->enum('dia_semana', ['Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes'])->nullable();
            $table->timestamps();

            $table->foreign('id_estancia')
                  ->references('id_estancia')
                  ->on('estancia')
                  ->onDelete('cascade');
        });

        // Franjas horarias
        Schema::create('horario', function (Blueprint $table) {
            $table->id('id_horario');
            $table->unsignedBigInteger('id_horario_semanal');
            $table->integer('hora_inicial');
            $table->integer('hora_final');
            $table->timestamps();

            $table->foreign('id_horario_semanal')
                  ->references('id_horario_semanal')
                  ->on('horario_semanal')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('horario');
        Schema::dropIfExists('horario_semanal');
    }
};