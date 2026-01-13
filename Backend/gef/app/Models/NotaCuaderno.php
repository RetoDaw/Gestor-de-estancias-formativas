<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotaCuaderno extends Model
{
    use HasFactory;

    protected $table = 'nota_cuaderno';
    protected $primaryKey = 'id_nota_cuaderno';

    protected $fillable = [
        'id_cuaderno',
        'id_tutor',
        'nota',
        'fecha_evaluacion',
        'comentarios'
    ];

    protected $casts = [
        'nota' => 'decimal:2',
        'fecha_evaluacion' => 'date',
    ];

    //Relaciones

    /**
     * Relación 1:1 inversa - Una nota pertenece a un cuaderno
     */
    public function cuaderno()
    {
        return $this->belongsTo(CuadernoPracticas::class, 'id_cuaderno', 'id_cuaderno');
    }

    /**
     * Relación N:1 - Una nota es evaluada por un tutor
     */
    public function tutor()
    {
        return $this->belongsTo(Usuario::class, 'id_tutor', 'id_usuario');
    }

}