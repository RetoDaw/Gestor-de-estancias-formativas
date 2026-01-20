<?php

namespace App\Services\Notas;

use App\Models\NotaAsignaturaCentro;

class NotaAsignaturaService
{
    public function obtenerNotaCentro(int $idAlumno, int $idAsignatura): float
    {
        $nota = NotaAsignaturaCentro::where('id_alumno', $idAlumno)
            ->where('id_asignatura', $idAsignatura)
            ->value('nota');

        if ($nota === null) {
            throw new \Exception('La nota de asignatura es obligatoria');
        }

        return (float) $nota;
    }
}