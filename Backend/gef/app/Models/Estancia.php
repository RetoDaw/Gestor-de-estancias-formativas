<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estancia extends Model
{
    use HasFactory;

    protected $table = 'estancia';
    protected $primaryKey = 'id_estancia';

    protected $fillable = [
        'id_alumno',
        'id_empresa',
        'id_tutor_empresa',
        'id_tutor_centro',
        'fecha_inicio',
        'fecha_fin',
        'horas_totales',
        'dias_totales'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    //Relaciones

    /**
     * Relación N:1 - Una estancia pertenece a un alumno
     */
    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'id_alumno', 'id_alumno');
    }

    /**
     * Relación N:1 - Una estancia pertenece a una empresa
     */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa', 'id_empresa');
    }

    /**
     * Relación N:1 - Tutor de empresa asignado
     */
    public function tutorEmpresa()
    {
        return $this->belongsTo(User::class, 'id_tutor_empresa', 'id_usuario');
    }

    /**
     * Relación N:1 - Tutor del centro asignado
     */
    public function tutorCentro()
    {
        return $this->belongsTo(User::class, 'id_tutor_centro', 'id_usuario');
    }

    /**
     * Relación 1:N - Horarios semanales de la estancia
     */
    public function horariosSemales ()
    {
        return $this->hasMany(HorarioSemanal::class, 'id_estancia', 'id_estancia');
    }

    /**
     * Relación 1:N - Seguimientos de la estancia
     */
    public function seguimientos()
    {
        return $this->hasMany(Seguimiento::class, 'id_estancia', 'id_estancia');
    }

    /**
     * Relación 1:N - Notas de resultados de aprendizaje
     */
    public function notasResultadosAprendizaje()
    {
        return $this->hasMany(NotaResultadoAprendizaje::class, 'id_estancia', 'id_estancia');
    }

    /**
     * Relación 1:N - Notas de competencias transversales
     */
    public function notasCompetenciasTransversales()
    {
        return $this->hasMany(NotaCompetenciaTransversal::class, 'id_estancia', 'id_estancia');
    }

    /**
     * Relación 1:N - Cuadernos de prácticas
     */
    public function cuadernosPracticas()
    {
        return $this->hasMany(CuadernoPracticas::class, 'id_estancia', 'id_estancia');
    }


    //prueba
    /**
     * Obtiene todas las asignaturas evaluadas en esta estancia
     */
    public function asignaturasEvaluadas()
    {
        $resultadosIds = $this->notasResultadosAprendizaje()
            ->pluck('id_resultado')
            ->unique();

        $asignaturasIds = ResultadoAprendizaje::whereIn('id_resultado', $resultadosIds)
            ->pluck('id_asignatura')
            ->unique();

        return Asignatura::whereIn('id_asignatura', $asignaturasIds)->get();
    }

     /**
     * Calcula la nota media de competencias transversales
     */
    public function notaMediaTransversales(): ?float
    {
        return $this->notasCompetenciasTransversales()
            ->whereNotNull('nota')
            ->avg('nota');
    }

}