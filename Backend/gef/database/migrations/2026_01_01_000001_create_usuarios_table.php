<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabla base de usuarios del sistema
     */
    public function up(): void
    {
        Schema::create('usuario', function (Blueprint $table) {
            $table->id('id_usuario');
            $table->string('email', 100)->unique();
            $table->string('password_hash', 255);
            $table->string('nombre', 100);
            $table->string('apellidos', 150);
            $table->string('telefono', 20)->nullable();
            $table->enum('tipo_usuario', ['ALUMNO', 'TUTOR_CENTRO', 'TUTOR_EMPRESA']);
            $table->timestamps(); // Añade created_at y updated_at automáticamente
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuario');
    }
};