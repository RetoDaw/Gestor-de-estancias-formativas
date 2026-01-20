<?php

namespace App\Services\Notas;

use App\Models\NotaResultadoAprendizaje;
use App\Models\NotaCompetenciaTransversal;
use App\Models\NotaCuaderno;
use App\Models\Estancia;

class NotaEmpresaService
{
    public function calcularNotaEmpresa(int $idAlumno, int $idAsignatura): float
    {
        $idEstancia = Estancia::where('id_alumno', $idAlumno)
            ->latest('fecha_inicio')
            ->value('id_estancia');

        if (!$idEstancia) {
            return 0;
        }

        $notaTecnica = $this->notaTecnica($idEstancia, $idAsignatura);
        $notaTransversal = $this->notaTransversal($idEstancia);
        $notaCuaderno = $this->notaCuaderno($idEstancia);

        return $this->aplicarPonderaciones(
            $notaTecnica,
            $notaTransversal,
            $notaCuaderno
        );
    }

    private function aplicarPonderaciones(
        ?float $notaTecnica,
        ?float $notaTransversal,
        ?float $notaCuaderno
    ): float {
        // CASO ESPECIAL: no hay nota tecnica
        if ($notaTecnica === null) {
            return round(
                ($notaTransversal * 0.80) +
                ($notaCuaderno * 0.20),
                2
            );
        }

        // CASO NORMAL
        return round(
            ($notaTecnica * 0.60) +
            ($notaTransversal * 0.20) +
            ($notaCuaderno * 0.20),
            2
        );
    }

     private function notaTecnica(int $idEstancia, int $idAsignatura): ?float
    {
        $notas = NotaResultadoAprendizaje::where('id_estancia', $idEstancia)
            ->whereHas('resultadoAprendizaje', function ($q) use ($idAsignatura) {
                $q->where('id_asignatura', $idAsignatura);
            })
            ->pluck('nota');

        return $notas->isEmpty()
            ? null
            : round($notas->avg(), 2);
    }

        private function notaTransversal(int $idEstancia): ?float
    {
        $notas = NotaCompetenciaTransversal::where('id_estancia', $idEstancia)
            ->pluck('nota');

        return $notas->isEmpty()
            ? null
            : round($notas->avg(), 2);
    }

        private function notaCuaderno(int $idEstancia): float
    {
        return NotaCuaderno::whereHas('cuaderno', function ($q) use ($idEstancia) {
                $q->where('id_estancia', $idEstancia);
            })
            ->value('nota') ?? 0;
    }
}