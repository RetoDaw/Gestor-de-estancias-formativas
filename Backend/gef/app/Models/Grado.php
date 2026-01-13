<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grado extends Model
{
    use HasFactory;

    protected $table = 'grado';
    protected $primaryKey = 'id_grado';

    protected $fillable = [
        'nombre',
        'familia',
        'codigo'
    ];

    //Relaciones
    
    /**
     * Relación 1:N - Un grado tiene muchos alumnos
     */
    public function alumnos()
    {
        return $this->hasMany(Alumno::class, 'id_grado', 'id_grado');
    }

    /**
     * Relación 1:N - Un grado tiene muchas asignaturas
     */
    public function asignaturas()
    {
        return $this->hasMany(Asignatura::class, 'id_grado', 'id_grado');
    }

    /**
     * Relación 1:N - Un grado tiene muchas competencias técnicas
     */
    public function competenciasTecnicas()
    {
        return $this->hasMany(CompetenciaTecnica::class, 'id_grado', 'id_grado');
    }

    /**
     * Relación 1:N - Un grado tiene muchas entregas
     */
    public function entregas()
    {
        return $this->hasMany(Entrega::class, 'id_grado', 'id_grado');
    }
}
