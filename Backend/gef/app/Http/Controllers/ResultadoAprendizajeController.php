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
            'id_grado' => 'required|integer',
            'competencias' => 'required|array',
            'competencias.*.descripcion' => 'required|string',
        ]);
        DB::transaction(function () use ($request){
            $ra = ResultadoAprendizaje::create([
                'descripcion' => $request->descripcion,
                'id_grado' => $request->id_grado,
            ]);
            foreach ($request->competencias as $compData) {
                Competencia::create([
                    'descripcion' => $compData['descripcion'],
                    'resultado_aprendizaje_id' => $ra->id,
                ]);
            }
        });
        return response()->json(['message' => 'Resultado de Aprendizaje guardado correctamente.'], 201);


    }

   
}
