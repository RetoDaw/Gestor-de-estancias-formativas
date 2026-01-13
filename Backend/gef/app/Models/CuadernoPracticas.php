<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CuadernoPracticas extends Model
{
    use HasFactory;

    protected $table = 'cuaderno_practicas';
    protected $primaryKey = 'id_cuaderno';

    protected $fillable = [
        'id_estancia',
        'id_entrega',
        'fecha_entrega',
        'archivo_pdf'
    ];

    protected $casts = [
        'fecha_entrega' => 'date',
    ];

    //Relaciones

    /**
     * Relación N:1 - Un cuaderno pertenece a una estancia
     */
    public function estancia()
    {
        return $this->belongsTo(Estancia::class, 'id_estancia', 'id_estancia');
    }

    /**
     * Relación N:1 - Un cuaderno pertenece a una entrega
     */
    public function entrega()
    {
        return $this->belongsTo(Entrega::class, 'id_entrega', 'id_entrega');
    }

    /**
     * Relación 1:1 - Un cuaderno tiene una nota
     */
    public function notaCuaderno()
    {
        return $this->hasOne(NotaCuaderno::class, 'id_cuaderno', 'id_cuaderno');
    }


}