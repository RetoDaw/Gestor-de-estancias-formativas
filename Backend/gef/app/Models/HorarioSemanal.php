<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HorarioSemanal extends Model
{
    use HasFactory;

    protected $table = 'horario_semanal';
    protected $primaryKey = 'id_horario_semanal';

    protected $fillable = [
        'id_estancia',
        'dia_semana'
    ];

    //Relaciones

    /**
     * Relación N:1 - Un horario semanal pertenece a una estancia
     */
    public function estancia()
    {
        return $this->belongsTo(Estancia::class, 'id_estancia', 'id_estancia');
    }

    /**
     * Relación 1:N - Un día tiene muchas franjas horarias
     */
    public function horarios()
    {
        return $this->hasMany(Horario::class, 'id_horario_semanal', 'id_horario_semanal');
    }
 
}