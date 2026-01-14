<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Sistema de seguimiento de comunicaciones
     */
    public function up(): void
    {
        Schema::create('seguimiento', function (Blueprint $table) {
            $table->id('id_seguimiento');
            $table->unsignedBigInteger('id_estancia');
            $table->date('dia');
            $table->time('hora');
            $table->text('accion');
            $table->unsignedBigInteger('id_receptor');
            $table->unsignedBigInteger('id_emisor');
            $table->enum('medio', ['EMAIL', 'EN_PERSONA', 'TELEFONO', 'VIDEOLLAMADA', 'OTRO']);
            $table->timestamps();

            // Foreign keys
            $table->foreign('id_estancia')
                  ->references('id_estancia')
                  ->on('estancia')
                  ->onDelete('cascade');

            $table->foreign('id_receptor')
                  ->references('id_usuario')
                  ->on('users');

            $table->foreign('id_emisor')
                  ->references('id_usuario')
                  ->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seguimiento');
    }
};