<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotaResultadoAprendizaje extends Model
{
    use HasFactory;

    protected $table = 'nota_resultado_aprendizaje';
    protected $primaryKey = 'id_nota_ra';

    protected $fillable = [
        'id_estancia',
        'id_competencia',
        'id_resultado',
        'nota'
    ];

    protected $casts = [
        'nota' => 'decimal:2',
    ];

    // Relaciones

    /**
     * Relación N:1 - Una nota pertenece a una estancia
     */
    public function estancia()
    {
        return $this->belongsTo(Estancia::class, 'id_estancia', 'id_estancia');
    }

    /**
     * Relación N:1 - La competencia técnica que generó esta nota
     */
    public function competenciaTecnica()
    {
        return $this->belongsTo(CompetenciaTecnica::class, 'id_competencia', 'id_competencia');
    }

    /**
     * Relación N:1 - El resultado de aprendizaje evaluado
     */
    public function resultadoAprendizaje()
    {
        return $this->belongsTo(ResultadoAprendizaje::class, 'id_resultado', 'id_resultado');
    }

    /**
     * Obtiene la asignatura de este RA (a través del RA)
     */
    public function asignatura()
    {
        return $this->resultadoAprendizaje->asignatura;
    }


}