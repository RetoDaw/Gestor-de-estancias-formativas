<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotaCompetenciaTransversal extends Model
{
    use HasFactory;

    protected $table = 'nota_competencia_transversal';
    protected $primaryKey = 'id_nota_trans';

    protected $fillable = [
        'id_estancia',
        'id_competencia_trans',
        'nota'
    ];

    protected $casts = [
        'nota' => 'decimal:2',
    ];

    //Relaciones

    /**
     * Relación N:1 - Una nota pertenece a una estancia
     */
    public function estancia()
    {
        return $this->belongsTo(Estancia::class, 'id_estancia', 'id_estancia');
    }

    /**
     * Relación N:1 - Una nota pertenece a una competencia transversal
     */
    public function competenciaTransversal()
    {
        return $this->belongsTo(
            CompetenciaTransversal::class,
            'id_competencia_trans',
            'id_competencia_trans'
        );
    }

}