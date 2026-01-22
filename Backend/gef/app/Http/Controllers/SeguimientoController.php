<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Seguimiento;
use App\Models\Estancia;
use Illuminate\Validation\ValidationException;

class SeguimientoController extends Controller
{
    /**
     * Listar seguimientos según permisos del usuario
     * - Alumnos: solo sus seguimientos
     * - Tutores: seguimientos de estancias donde tienen alumnos asignados
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $id_estancia = $request->query('id_estancia');
        
        if (!$id_estancia) {
            return response()->json(['message' => 'ID de estancia requerido'], 400);
        }

        // Verificar permisos según tipo de usuario
        $estancia = Estancia::find($id_estancia);
        
        if (!$estancia) {
            return response()->json(['message' => 'Estancia no encontrada'], 404);
        }

        // Control de acceso
        if ($user->esAlumno()) {
            // Alumnos solo ven seguimientos de sus propias estancias
            if ($estancia->id_alumno != $user->id_usuario) {
                return response()->json(['message' => 'No autorizado'], 403);
            }
        } elseif ($user->esTutorCentro()) {
            // Tutores de centro ven seguimientos de estancias donde son tutores
            if ($estancia->id_tutor_centro != $user->id_usuario) {
                return response()->json(['message' => 'No autorizado'], 403);
            }
        } elseif ($user->esTutorEmpresa()) {
            // Tutores de empresa ven seguimientos de estancias donde son tutores
            if ($estancia->id_tutor_empresa != $user->id_usuario) {
                return response()->json(['message' => 'No autorizado'], 403);
            }
        }
        
        $seguimientos = Seguimiento::where('id_estancia', $id_estancia)
            ->orderBy('dia', 'desc')
            ->orderBy('hora', 'desc')
            ->get();
        
        return response()->json($seguimientos);
    }

    /**
     * Crear nuevo seguimiento
     * Solo tutores (centro o empresa) pueden crear
     */
    public function store(Request $request)
    {
        $user = $request->user();

        // Verificar que sea tutor
        if (!$user->esTutorCentro() && !$user->esTutorEmpresa()) {
            return response()->json(['message' => 'Solo los tutores pueden crear seguimientos'], 403);
        }

        try {
            $validatedData = $request->validate([
                'id_estancia' => 'required|integer|exists:estancia,id_estancia',
                'dia' => 'required|date',
                'hora' => 'required',
                'accion' => 'required|string',
                'id_emisor' => 'required|integer|exists:users,id_usuario',
                'id_receptor' => 'required|integer|exists:users,id_usuario',
                'medio' => 'required|in:EMAIL,TELEFONO,EN_PERSONA,VIDEOLLAMADA,OTRO',
            ]);

            // Verificar que el tutor tenga acceso a esta estancia
            $estancia = Estancia::find($validatedData['id_estancia']);
            
            if ($user->esTutorCentro() && $estancia->id_tutor_centro != $user->id_usuario) {
                return response()->json(['message' => 'No tienes acceso a esta estancia'], 403);
            }
            
            if ($user->esTutorEmpresa() && $estancia->id_tutor_empresa != $user->id_usuario) {
                return response()->json(['message' => 'No tienes acceso a esta estancia'], 403);
            }

            $seguimiento = Seguimiento::create($validatedData);

            return response()->json($seguimiento, 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    /**
     * Actualizar seguimiento existente
     * Solo el creador o tutores de la estancia pueden editar
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();

        // Verificar que sea tutor
        if (!$user->esTutorCentro() && !$user->esTutorEmpresa()) {
            return response()->json(['message' => 'Solo los tutores pueden editar seguimientos'], 403);
        }

        try {
            $seguimiento = Seguimiento::findOrFail($id);
            $estancia = $seguimiento->estancia;

            // Verificar acceso a la estancia
            if ($user->esTutorCentro() && $estancia->id_tutor_centro != $user->id_usuario) {
                return response()->json(['message' => 'No tienes acceso a esta estancia'], 403);
            }
            
            if ($user->esTutorEmpresa() && $estancia->id_tutor_empresa != $user->id_usuario) {
                return response()->json(['message' => 'No tienes acceso a esta estancia'], 403);
            }
            
            $validatedData = $request->validate([
                'dia' => 'required|date',
                'hora' => 'required',
                'accion' => 'required|string',
                'id_emisor' => 'required|integer|exists:users,id_usuario',
                'id_receptor' => 'required|integer|exists:users,id_usuario',
                'medio' => 'required|in:EMAIL,TELEFONO,EN_PERSONA,VIDEOLLAMADA,OTRO',
            ]);

            $seguimiento->update($validatedData);

            return response()->json($seguimiento);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    /**
     * Eliminar seguimiento
     * Solo tutores de la estancia pueden eliminar
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();

        // Verificar que sea tutor
        if (!$user->esTutorCentro() && !$user->esTutorEmpresa()) {
            return response()->json(['message' => 'Solo los tutores pueden eliminar seguimientos'], 403);
        }

        $seguimiento = Seguimiento::findOrFail($id);
        $estancia = $seguimiento->estancia;

        // Verificar acceso a la estancia
        if ($user->esTutorCentro() && $estancia->id_tutor_centro != $user->id_usuario) {
            return response()->json(['message' => 'No tienes acceso a esta estancia'], 403);
        }
        
        if ($user->esTutorEmpresa() && $estancia->id_tutor_empresa != $user->id_usuario) {
            return response()->json(['message' => 'No tienes acceso a esta estancia'], 403);
        }

        $seguimiento->delete();

        return response()->json(['message' => 'Seguimiento eliminado'], 200);
    }
}