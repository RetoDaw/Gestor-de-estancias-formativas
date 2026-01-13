<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    use HasFactory;

    protected $table = 'horario';
    protected $primaryKey = 'id_horario';

    protected $fillable = [
        'id_horario_semanal',
        'hora_inicial',
        'hora_final'
    ];

    protected $casts = [
        'hora_inicial' => 'integer',
        'hora_final' => 'integer',
    ];

    //Relaciones

    /**
     * Relación N:1 - Un horario pertenece a un horario semanal
     */
    public function horarioSemanal()
    {
        return $this->belongsTo(HorarioSemanal::class, 'id_horario_semanal', 'id_horario_semanal');
    }

   
}