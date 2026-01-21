<?php

namespace App\Services\Notas;

class CalculoNotaFinalService
{
    protected NotaAsignaturaService $notaAsignaturaService;
    protected NotaEmpresaService $notaEmpresaService;

    public function __construct(
        NotaAsignaturaService $notaAsignaturaService,
        NotaEmpresaService $notaEmpresaService
    ) {
        $this->notaAsignaturaService = $notaAsignaturaService;
        $this->notaEmpresaService = $notaEmpresaService;
    }

    public function calcular(int $idAlumno, int $idAsignatura): float
    {
        $notaCentro = $this->notaAsignaturaService
            ->obtenerNotaCentro($idAlumno, $idAsignatura);

        $notaEmpresa = $this->notaEmpresaService
            ->calcularNotaEmpresa($idAlumno, $idAsignatura);

        $notaFinal = 
            ($notaCentro * 0.80) +
            ($notaEmpresa * 0.20);

        return round($notaFinal, 2);
    }
}