<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asignatura;
class AsignaturaController extends Controller
{
    public function obtenerAsignaturasporGrado($id_grado)
    {
        $asignaturas = Asignatura::where('id_grado', $id_grado)->get();
        return response()->json($asignaturas);
    }
}
