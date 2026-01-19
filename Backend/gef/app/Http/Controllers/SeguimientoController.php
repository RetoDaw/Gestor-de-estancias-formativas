<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Seguimiento;
use Illuminate\Validation\ValidationException;

class SeguimientoController extends Controller
{
    public function index(Request $request)
    {
        $id_estancia = $request->query('id_estancia');
        
        $seguimientos = Seguimiento::where('id_estancia', $id_estancia)
            ->orderBy('dia', 'desc')
            ->orderBy('hora', 'desc')
            ->get();
        
        return response()->json($seguimientos);
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'id_estancia' => 'required|integer|exists:estancias,id_estancia',
                'dia' => 'required|date',
                'hora' => 'required',
                'accion' => 'required|string',
                'id_emisor' => 'required|integer|exists:usuarios,id',
                'id_receptor' => 'required|integer|exists:usuarios,id',
                'medio' => 'required|in:EMAIL,TELEFONO,EN_PERSONA,VIDEOLLAMADA,OTRO',
            ]);

            $seguimiento = Seguimiento::create($validatedData);

            return response()->json($seguimiento, 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $seguimiento = Seguimiento::findOrFail($id);
            
            $validatedData = $request->validate([
                'dia' => 'required|date',
                'hora' => 'required',
                'accion' => 'required|string',
                'id_emisor' => 'required|integer|exists:usuarios,id',
                'id_receptor' => 'required|integer|exists:usuarios,id',
                'medio' => 'required|in:EMAIL,TELEFONO,EN_PERSONA,VIDEOLLAMADA,OTRO',
            ]);

            $seguimiento->update($validatedData);

            return response()->json($seguimiento);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    public function destroy($id)
    {
        $seguimiento = Seguimiento::findOrFail($id);
        $seguimiento->delete();

        return response()->json(['message' => 'Seguimiento eliminado'], 200);
    }
}