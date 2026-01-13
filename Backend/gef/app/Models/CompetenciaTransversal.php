<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompetenciaTransversal extends Model
{
    use HasFactory;

    protected $table = 'competencia_transversal';
    protected $primaryKey = 'id_competencia_trans';

    protected $fillable = [
        'descripcion'
    ];

    //Relaciones

    /**
     * Relación 1:N - Una competencia transversal tiene muchas notas
     */
    public function notas()
    {
        return $this->hasMany(
            NotaCompetenciaTransversal::class,
            'id_competencia_trans',
            'id_competencia_trans'
        );
    }

}
