<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $table = 'empresa';
    protected $primaryKey = 'id_empresa';
    public $incrementing = true;
    
    protected $fillable = [
        'cif',
        'nombre',
        'poblacion',
        'telefono',
        'email'
    ];

    // Relaciones

    /**
     * Relación 1:N - Una empresa tiene muchas estancias
     */
    public function estancias()
    {
        return $this->hasMany(Estancia::class, 'id_empresa', 'id_empresa');
    }

    /**
     * Obtener la estancia activa actual (si existe)
     */
    public function estanciaActiva()
    {
        return $this->hasOne(Estancia::class, 'id_empresa', 'id_empresa')
            ->whereDate('fecha_inicio', '<=', now())
            ->whereDate('fecha_fin', '>=', now())
            ->latest();
    }

    /**
     * Obtener todos los tutores de empresa que han trabajado con esta empresa
     */
    public function tutoresEmpresa()
    {
        return $this->hasManyThrough(
            User::class,
            Estancia::class,
            'id_empresa',
            'id_usuario',
            'id_empresa',
            'id_tutor_empresa'
        )->distinct();
    }
}