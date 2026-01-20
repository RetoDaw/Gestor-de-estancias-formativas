<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ResultadoAprendizaje;
use App\Models\Competencia;
use DB;
class ResultadoAprendizajeController extends Controller
{
    
    public function store(Request $request)
    
    {
        $request->validate([
            'descripcion' => 'required|string',
            'id_asignatura' => 'required|integer',
            'id_grado' => 'required|integer',
        ]);
        DB::transaction(function () use ($request){
            $ra = ResultadoAprendizaje::create([
                'descripcion' => $request->descripcion,
                'id_grado' => $request->id_grado,
                'id_asignatura' => $request->id_asignatura,
            ]);
        });
        return response()->json(['message' => 'Resultado de Aprendizaje guardado correctamente.'], 201);


    }

   
}
