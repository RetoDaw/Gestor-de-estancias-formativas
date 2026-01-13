<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResultadoAprendizaje extends Model
{
    use HasFactory;

    protected $table = 'resultado_aprendizaje';
    protected $primaryKey = 'id_resultado';

    protected $fillable = [
        'id_asignatura',
        'descripcion'
    ];

    //Relaciones

    /**
     * Relación N:1 - Un RA pertenece a una asignatura
     */
    public function asignatura()
    {
        return $this->belongsTo(Asignatura::class, 'id_asignatura', 'id_asignatura');
    }

    /**
     * Relación N:M - Un RA puede estar relacionado con muchas competencias técnicas 
     */
    public function competenciasTecnicas()
    {
        return $this->belongsToMany(
            CompetenciaTecnica::class,
            'competencia_tecnica_resultado',
            'id_resultado',
            'id_competencia'
        );
    }

    /**
     * Relación 1:N - Notas de este RA en las empresas
     */
    public function notasEmpresa()
    {
        return $this->hasMany(NotaResultadoAprendizaje::class, 'id_resultado', 'id_resultado');
    }


}