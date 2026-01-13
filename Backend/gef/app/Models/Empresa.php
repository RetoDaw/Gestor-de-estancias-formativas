<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    use HasFactory;

    protected $table = 'empresa';
    protected $primaryKey = 'id_empresa';

    protected $fillable = [
        'cif',
        'nombre',
        'poblacion',
        'telefono',
        'email'
    ];

    //Relaciones

    /**
     * Relación 1:N - Una empresa tiene muchas estancias
     */
    public function estancias()
    {
        return $this->hasMany(Estancia::class, 'id_empresa', 'id_empresa');
    }

}