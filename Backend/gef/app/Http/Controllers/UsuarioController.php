<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Alumno;
use App\Models\Grado;
use Illuminate\Validation\ValidationException;

class UsuarioController extends Controller
{
    /**
     * Obtener datos del usuario autenticado con información del grado
     */
    public function getAuthUser(Request $request)
    {
        $user = $request->user();
        
        // Si es alumno, incluir información del grado
        if ($user->esAlumno()) {
            $alumno = Alumno::with('grado')->where('id_alumno', $user->id_usuario)->first();
            
            if ($alumno && $alumno->grado) {
                $userData = [
                    'id_usuario' => $user->id_usuario,
                    'email' => $user->email,
                    'nombre' => $user->nombre,
                    'apellidos' => $user->apellidos,
                    'telefono' => $user->telefono,
                    'grado' => $alumno->grado->nombre,
                    'familia' => $alumno->grado->familia,
                    'codigo_grado' => $alumno->grado->codigo
                ];
                
                return response()->json($userData);
            }
        }
        
        // Si no es alumno o no tiene grado, devolver solo datos básicos
        return response()->json([
            'id_usuario' => $user->id_usuario,
            'email' => $user->email,
            'nombre' => $user->nombre,
            'apellidos' => $user->apellidos,
            'telefono' => $user->telefono
        ]);
    }

    /**
     * Listar todos los grados que tienen alumnos (para el selector)
     */
    public function listarGradosConAlumnos(Request $request)
    {
        $user = $request->user();
        
        // Verificar que sea tutor
        if (!$user->esTutorCentro() && !$user->esTutorEmpresa()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }
        
        // Obtener grados que tienen al menos un alumno
        $grados = Grado::whereHas('alumnos')
            ->get(['id_grado', 'nombre', 'familia', 'codigo'])
            ->map(function ($grado) {
                return [
                    'id_grado' => $grado->id_grado,
                    'nombre' => $grado->nombre,
                    'familia' => $grado->familia,
                    'codigo' => $grado->codigo,
                    'nombre_completo' => $grado->nombre . ' (' . $grado->codigo . ')'
                ];
            });
        
        return response()->json($grados);
    }

    /**
     * Listar alumnos por grado (para el selector en cascada)
     */
    public function listarAlumnosPorGrado(Request $request)
    {
        $user = $request->user();
        
        // Verificar que sea tutor
        if (!$user->esTutorCentro() && !$user->esTutorEmpresa()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }
        
        $gradoId = $request->query('id_grado');
        
        if (!$gradoId) {
            return response()->json(['message' => 'ID de grado requerido'], 400);
        }
        
        // Obtener alumnos del grado específico
        $alumnos = Alumno::with(['usuario', 'grado'])
            ->where('id_grado', $gradoId)
            ->get()
            ->map(function ($alumno) {
                return [
                    'id_usuario' => $alumno->id_alumno,
                    'nombre_completo' => $alumno->usuario->nombre . ' ' . $alumno->usuario->apellidos,
                    'email' => $alumno->usuario->email,
                    'grado' => $alumno->grado ? $alumno->grado->nombre : 'Sin grado'
                ];
            });
        
        return response()->json($alumnos);
    }

    /**
     * Obtener datos de un alumno específico con su grado
     */
    public function getAlumno(Request $request)
    {
        $userId = $request->query('user_id');
        
        // Verificar que el usuario autenticado sea tutor o sea el mismo alumno
        $authUser = $request->user();
        if (!$authUser->esTutorCentro() && 
            !$authUser->esTutorEmpresa() && 
            $authUser->id_usuario != $userId) {
            return response()->json(['message' => 'No autorizado'], 403);
        }
        
        $user = User::find($userId);
        
        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }
        
        // Si es alumno, incluir información del grado
        if ($user->esAlumno()) {
            $alumno = Alumno::with('grado')->where('id_alumno', $userId)->first();
            
            if ($alumno && $alumno->grado) {
                return response()->json([
                    'id_usuario' => $user->id_usuario,
                    'email' => $user->email,
                    'nombre' => $user->nombre,
                    'apellidos' => $user->apellidos,
                    'telefono' => $user->telefono,
                    'grado' => $alumno->grado->nombre,
                    'familia' => $alumno->grado->familia,
                    'codigo_grado' => $alumno->grado->codigo
                ]);
            }
        }
        
        // Si no es alumno o no tiene grado
        return response()->json([
            'id_usuario' => $user->id_usuario,
            'email' => $user->email,
            'nombre' => $user->nombre,
            'apellidos' => $user->apellidos,
            'telefono' => $user->telefono
        ]);
    }

    public function search()
    {
        $userId = request()->query('user_id');
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }
        return response()->json($user);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'nombre' => 'required|string|max:100',
            'apellidos' => 'required|string|max:150',
            'telefono' => 'nullable|string|max:20',
            'tipo_usuario' => 'required|in:ALUMNO,TUTOR_CENTRO,TUTOR_EMPRESA',
        ]);

        $user = User::create([
            'email' => $validatedData['email'],
            'password_hash' => Hash::make($validatedData['password']),
            'nombre' => $validatedData['nombre'],
            'apellidos' => $validatedData['apellidos'],
            'telefono' => $validatedData['telefono'] ?? null,
            'tipo_usuario' => $validatedData['tipo_usuario'],
        ]);

        return response()->json(['message' => 'Usuario creado con éxito', 'user' => $user], 201);
    }

    public function esAlumno(Request $request)
    {
        return response()->json(
            $request->user()->esAlumno()
        );
    }

    public function esTutorCentro(Request $request)
    {
        return response()->json(
            $request->user()->esTutorCentro()
        );
    }
}