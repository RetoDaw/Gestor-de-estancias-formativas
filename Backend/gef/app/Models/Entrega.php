<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entrega extends Model
{
    use HasFactory;

    protected $table = 'entrega';
    protected $primaryKey = 'id_entrega';

    protected $fillable = [
        'id_tutor',
        'id_grado'
    ];

    //Relaciones

    /**
     * Relación N:1 - Una entrega es creada por un tutor
     */
    public function tutor()
    {
        return $this->belongsTo(Usuario::class, 'id_tutor', 'id_usuario');
    }

    /**
     * Relación N:1 - Una entrega pertenece a un grado
     */
    public function grado()
    {
        return $this->belongsTo(Grado::class, 'id_grado', 'id_grado');
    }

    /**
     * Relación 1:N - Una entrega tiene muchos cuadernos
     */
    public function cuadernosPracticas()
    {
        return $this->hasMany(CuadernoPracticas::class, 'id_entrega', 'id_entrega');
    }

}