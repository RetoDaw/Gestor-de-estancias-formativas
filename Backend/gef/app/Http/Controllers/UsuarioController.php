<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Validation\ValidationException;
class UsuarioController extends Controller
{
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
            'tipo_usuario' => 'required|in:tutor_centro,tutor_empresa,admin',
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
