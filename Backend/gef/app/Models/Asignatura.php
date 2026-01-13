<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asignatura extends Model
{
    use HasFactory;

    protected $table = 'asignatura';
    protected $primaryKey = 'id_asignatura';

    protected $fillable = [
        'id_grado',
        'nombre'
    ];

    //Relaciones

    /**
     * Relación N:1 - Una asignatura pertenece a un grado
     */
    public function grado()
    {
        return $this->belongsTo(Grado::class, 'id_grado', 'id_grado');
    }

    /**
     * Relación 1:N - Una asignatura tiene muchos resultados de aprendizaje
     */
    public function resultadosAprendizaje()
    {
        return $this->hasMany(ResultadoAprendizaje::class, 'id_asignatura', 'id_asignatura');
    }

    /**
     * Relación 1:N - Notas de esta asignatura en el centro
     */
    public function notasCentro()
    {
        return $this->hasMany(NotaAsignaturaCentro::class, 'id_asignatura', 'id_asignatura');
    }

}