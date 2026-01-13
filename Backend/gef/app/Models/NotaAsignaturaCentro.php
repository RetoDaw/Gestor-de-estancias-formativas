<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotaAsignaturaCentro extends Model
{
    use HasFactory;

    protected $table = 'nota_asignatura_centro';
    protected $primaryKey = 'id_nota';

    protected $fillable = [
        'id_alumno',
        'id_asignatura',
        'nota'
    ];

    protected $casts = [
        'nota' => 'decimal:2',
    ];

    //Relaciones

    /**
     * Relación N:1 - Una nota pertenece a un alumno
     */
    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'id_alumno', 'id_alumno');
    }

    /**
     * Relación N:1 - Una nota pertenece a una asignatura
     */
    public function asignatura()
    {
        return $this->belongsTo(Asignatura::class, 'id_asignatura', 'id_asignatura');
    }

}