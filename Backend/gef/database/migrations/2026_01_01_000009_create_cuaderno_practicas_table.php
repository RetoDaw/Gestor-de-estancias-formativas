<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cuaderno de prácticas y su evaluación
     */
    public function up(): void
    {
        // Entregas
        Schema::create('entrega', function (Blueprint $table) {
            $table->id('id_entrega');
            $table->unsignedBigInteger('id_tutor');
            $table->unsignedBigInteger('id_grado');
            $table->timestamps();

            $table->foreign('id_tutor')
                  ->references('id_usuario')
                  ->on('usuario')
                  ->onDelete('cascade');

            $table->foreign('id_grado')
                  ->references('id_grado')
                  ->on('grado')
                  ->onDelete('cascade');
        });

        // Cuadernos de prácticas
        Schema::create('cuaderno_practicas', function (Blueprint $table) {
            $table->id('id_cuaderno');
            $table->unsignedBigInteger('id_estancia');
            $table->unsignedBigInteger('id_entrega');
            $table->date('fecha_entrega');
            $table->string('archivo_pdf', 255); // Ruta al PDF
            $table->timestamps();

            $table->foreign('id_estancia')
                  ->references('id_estancia')
                  ->on('estancia')
                  ->onDelete('cascade');

            $table->foreign('id_entrega')
                  ->references('id_entrega')
                  ->on('entrega')
                  ->onDelete('cascade');
        });

        // Notas del cuaderno
        Schema::create('nota_cuaderno', function (Blueprint $table) {
            $table->id('id_nota_cuaderno');
            $table->unsignedBigInteger('id_cuaderno');
            $table->unsignedBigInteger('id_tutor');
            $table->decimal('nota', 4, 2)->nullable();
            $table->date('fecha_evaluacion')->nullable();
            $table->text('comentarios')->nullable();
            $table->timestamps();

            $table->unique('id_cuaderno', 'unique_cuaderno');

            $table->foreign('id_cuaderno')
                  ->references('id_cuaderno')
                  ->on('cuaderno_practicas')
                  ->onDelete('cascade');

            $table->foreign('id_tutor')
                  ->references('id_usuario')
                  ->on('usuario')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nota_cuaderno');
        Schema::dropIfExists('cuaderno_practicas');
        Schema::dropIfExists('entrega');
    }
};