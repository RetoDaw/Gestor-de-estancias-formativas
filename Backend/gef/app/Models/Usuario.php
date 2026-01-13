<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    use HasFactory;

    protected $table = 'usuario';
    protected $primaryKey = 'id_usuario';

    protected $fillable = [
        'email',
        'password_hash',
        'nombre',
        'apellidos',
        'telefono',
        'tipo_usuario'
    ]; 

    protected $hidden = [
        'password_hash'
    ];

    protected $casts = [
        'tipo_usuario' => 'string'
    ];

    //Relaciones

    /**
     * Relación 1:1 - Un usuario puede ser alumno
    */
    public function alumno()
    {
        return $this->hasOne(Alumno::class, 'id_alumno', 'id_usuario');
    }
    /**
     * Relación 1:N - Estancias donde este usuario es tutor de empresa
    */
    public function estanciasComoTutorEmpresa()
    {
        return $this->hasMany(Estancia::class, 'id_tutor_empresa', 'id_usuario');
    }
    /**
     * Relación 1:N - Estancias donde este usuario es tutor de centro
    */
    public function estanciasComoTutorCentro()
    {
        return $this->hasMany(Estancia::class, 'id_tutor_centro', 'id_usuario');
    }
    /**
     * Relación 1:N - Seguimientos donde este usuario es receptor
    */
    public function seguimientosRecibidos()
    {
        return $this->hasMany(Seguimiento::class, 'id_receptor', 'id_usuario');
    }
    /**
     * Relación 1:N - Seguimientos donde este usuario es emisor
    */
    public function seguimientosEnviados()
    {
        return $this->hasMany(Seguimiento::class, 'id_emisor', 'id_usuario');
    }
    /**
     * Relación 1:N - Entregas gestionadas por este tutor
    */
    public function entregas()
    {
        return $this->hasMany(Entrega::class,'id_tutor', 'id_usuario');
    }
    /**
     * Relación 1:N - Notas de cuaderno evaluadas por este tutor
    */
    public function notasCuadernoEvaluadas()
    {
        return $this->hasMany(NotaCuaderno::class,'id_tutor', 'id_usuario');
    }



}
