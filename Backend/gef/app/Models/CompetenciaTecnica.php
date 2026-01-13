<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompetenciaTecnica extends Model
{
    use HasFactory;

    protected $table = 'competencia_tecnica';
    protected $primaryKey = 'id_competencia';

    protected $fillable = [
        'id_grado',
        'descripcion'
    ];

    //Relaciones

    /**
     * Relación N:1 - Una competencia pertenece a un grado
     */
    public function grado()
    {
        return $this->belongsTo(Grado::class, 'id_grado', 'id_grado');
    }

    /**
     * Relación N:M - Una competencia se relaciona con muchos RAs
     */
    public function resultadosAprendizaje()
    {
        return $this->belongsToMany(
            ResultadoAprendizaje::class,
            'competencia_tecnica_resultado',
            'id_competencia',
            'id_resultado'
        );
    }

    /**
     * Relación 1:N - Notas de esta competencia en empresas
     */
    public function notasEmpresa()
    {
        return $this->hasMany(NotaResultadoAprendizaje::class, 'id_competencia', 'id_competencia');
    }

}