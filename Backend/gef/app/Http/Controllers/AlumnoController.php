<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // Usamos el modelo User que es el base
use App\Models\Alumno;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AlumnoController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6',
                'nombre' => 'required|string|max:100',
                'apellidos' => 'required|string|max:150',
                'telefono' => 'nullable|string|max:20',
                'id_grado' => 'required|integer|exists:grado,id_grado', 
            ]);

            $resultado = DB::transaction(function () use ($validatedData) {
                
                $user = User::create([
                    'email' => $validatedData['email'],
                    'password_hash' => Hash::make($validatedData['password']),
                    'nombre' => $validatedData['nombre'],
                    'apellidos' => $validatedData['apellidos'],
                    'telefono' => $validatedData['telefono'] ?? null,
                    'tipo_usuario' => 'alumno', // Forzamos que sea alumno
                ]);

                $alumno = Alumno::create([
                    'id_alumno' => $user->id_usuario, 
                    'id_grado'  => $validatedData['id_grado'],
                ]);

                return ['user' => $user, 'alumno' => $alumno];
            });

            return response()->json([
                'message' => 'Alumno creado con éxito',
                'data' => $resultado
            ], 201);
            
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}