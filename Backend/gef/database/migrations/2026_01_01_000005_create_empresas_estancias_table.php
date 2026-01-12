<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Empresas y estancias de prácticas
     */
    public function up(): void
    {
        // Empresas
        Schema::create('empresa', function (Blueprint $table) {
            $table->id('id_empresa');
            $table->string('cif', 20)->unique();
            $table->string('nombre', 150);
            $table->string('poblacion', 100);
            $table->string('telefono', 20);
            $table->string('email', 100);
            $table->timestamps();
        });

        // Estancias de prácticas
        Schema::create('estancia', function (Blueprint $table) {
            $table->id('id_estancia');
            $table->unsignedBigInteger('id_alumno');
            $table->unsignedBigInteger('id_empresa');
            $table->unsignedBigInteger('id_tutor_empresa')->nullable();
            $table->unsignedBigInteger('id_tutor_centro')->nullable();
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->integer('horas_totales');
            $table->integer('dias_totales');
            $table->timestamps();

            // Foreign keys
            $table->foreign('id_alumno')
                  ->references('id_alumno')
                  ->on('alumno')
                  ->onDelete('cascade');

            $table->foreign('id_empresa')
                  ->references('id_empresa')
                  ->on('empresa');

            $table->foreign('id_tutor_empresa')
                  ->references('id_usuario')
                  ->on('usuario');

            $table->foreign('id_tutor_centro')
                  ->references('id_usuario')
                  ->on('usuario');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estancia');
        Schema::dropIfExists('empresa');
    }
};