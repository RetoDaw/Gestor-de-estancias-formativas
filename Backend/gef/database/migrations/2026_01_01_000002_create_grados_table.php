<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla de grados 
        Schema::create('grado', function (Blueprint $table) {
            $table->id('id_grado');
            $table->string('nombre', 100);
            $table->string('familia', 100);
            $table->string('codigo', 20)->unique();
            $table->timestamps();
        });

        // Tabla de alumnos (relación 1:1 con usuario)
        Schema::create('alumno', function (Blueprint $table) {
            $table->unsignedBigInteger('id_alumno')->primary();
            $table->unsignedBigInteger('id_grado');
            $table->timestamps();

            // Foreign keys
            $table->foreign('id_alumno')
                  ->references('id_usuario')
                  ->on('users')
                  ->onDelete('cascade');
            
            $table->foreign('id_grado')
                  ->references('id_grado')
                  ->on('grado')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumno');
        Schema::dropIfExists('grado');
    }
};