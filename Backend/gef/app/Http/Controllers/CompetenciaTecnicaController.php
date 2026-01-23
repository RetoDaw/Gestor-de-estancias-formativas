<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CompetenciaTecnica;
use Illuminate\Support\Facades\DB;
class CompetenciaTecnicaController extends Controller
{
   
        public function store(Request $request)
        {
            $request->validate([
                'descripcion' => 'required|string',
                'id_grado' => 'required|integer|exists:grado,id_grado',
                'resultado_aprendizaje' => 'required|array',
                'resultado_aprendizaje.*' => 'integer|exists:resultado_aprendizaje,id_resultado',
            ]);
            try{
                DB::beginTransaction();
                $competencia = CompetenciaTecnica::create([
                    'descripcion' => $request->descripcion,
                    'id_grado' => $request->id_grado,
                ]);
                $competencia->resultadosAprendizaje()->attach($request->resultado_aprendizaje);
                DB::commit();
        
                }catch(\Exception $e){
                    DB::rollBack();
                    return response()->json(['message' => 'Error al crear la competencia técnica', 'error' => $e->getMessage()], 500);
        
                }
    
    }


    /**
     * Obtener todas las competencias técnicas de un grado
     */
    public function obtenerPorGrado($idGrado)
    {
        try {
            $competencias = CompetenciaTecnica::where('id_grado', $idGrado)
                ->select('id_competencia', 'descripcion')
                ->get();

            \Log::info('Competencias encontradas para grado ' . $idGrado, [
                'count' => $competencias->count(),
                'competencias' => $competencias
            ]);

            return response()->json($competencias);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener competencias técnicas: ' . $e->getMessage()
            ], 500);
        }
    }
}