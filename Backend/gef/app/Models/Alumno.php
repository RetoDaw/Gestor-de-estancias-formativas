<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    use HasFactory;

    protected $table = 'alumno';
    protected $primaryKey = 'id_alumno';
    public $incrementing = false;

    protected $fillable = [
        'id_alumno',
        'id_grado'
    ];

    //Relaciones

    /**
     * Relación 1:1 inversa - Un alumno es un usuario
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_alumno', 'id_usuario');
    }

    /**
     * Relación N:1 - Un alumno pertenece a un grado
     */
    public function grado()
    {
        return $this->belongsTo(Grado::class, 'id_grado', 'id_grado');
    }

    /**
     * Relación 1:N - Un alumno tiene muchas estancias
     */
    public function estancias()
    {
        return $this->hasMany(Estancia::class, 'id_alumno', 'id_alumno');
    }

    /**
     * Relación 1:N - Notas de asignaturas del centro
     */
    public function notasAsignaturasCentro()
    {
        return $this->hasMany(NotaAsignaturaCentro::class, 'id_alumno', 'id_alumno');
    }

    
}