<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens,HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
       'email',
        'password_hash',
        'nombre',
        'apellidos',
        'telefono',
        'tipo_usuario'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password_hash',
        'remember_token',
    ];
    protected $casts = [
        'tipo_usuario' => 'string'
    ];
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
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
