<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Seguimiento extends Model
{
    use HasFactory;

    protected $table = 'seguimiento';
    protected $primaryKey = 'id_seguimiento';

    protected $fillable = [
        'id_estancia',
        'dia',
        'hora',
        'accion',
        'id_receptor',
        'id_emisor',
        'medio'
    ];

    protected $casts = [
        'dia' => 'date',
        'hora' => 'datetime:H:i:s',
    ];

    //Relaciones

    /**
     * Relación N:1 - Un seguimiento pertenece a una estancia
     */
    public function estancia()
    {
        return $this->belongsTo(Estancia::class, 'id_estancia', 'id_estancia');
    }

    /**
     * Relación N:1 - Usuario que recibe la comunicación
     */
    public function receptor()
    {
        return $this->belongsTo(Usuario::class, 'id_receptor', 'id_usuario');
    }

    /**
     * Relación N:1 - Usuario que envía la comunicación
     */
    public function emisor()
    {
        return $this->belongsTo(Usuario::class, 'id_emisor', 'id_usuario');
    }


}