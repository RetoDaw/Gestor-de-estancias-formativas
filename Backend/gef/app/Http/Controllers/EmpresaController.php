<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Estancia;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    /**
     * Obtener la empresa del alumno actual (si tiene estancia activa)
     */
    public function getEmpresaAlumno(Request $request)
    {
        $userId = $request->query('user_id');
        
        // Buscar estancia activa del alumno
        $estancia = Estancia::with(['empresa', 'tutorEmpresa'])
            ->where('id_alumno', $userId)
            ->whereDate('fecha_inicio', '<=', now())
            ->whereDate('fecha_fin', '>=', now())
            ->first();
        
        if (!$estancia) {
            return response()->json([
                'tieneEmpresa' => false,
                'empresa' => null,
                'tutor' => null
            ]);
        }
        
        return response()->json([
            'tieneEmpresa' => true,
            'empresa' => [
                'id_empresa' => $estancia->empresa->id_empresa,
                'cif' => $estancia->empresa->cif,
                'nombre' => $estancia->empresa->nombre,
                'poblacion' => $estancia->empresa->poblacion,
                'telefono' => $estancia->empresa->telefono,
                'email' => $estancia->empresa->email,
            ],
            'tutor' => $estancia->tutorEmpresa ? [
                'nombre' => $estancia->tutorEmpresa->nombre,
                'apellidos' => $estancia->tutorEmpresa->apellidos,
                'email' => $estancia->tutorEmpresa->email,
                'telefono' => $estancia->tutorEmpresa->telefono,
            ] : null
        ]);
    }

    /**
     * Listar todas las empresas disponibles
     */
    public function index()
    {
        $empresas = Empresa::all();
        return response()->json($empresas);
    }

    /**
     * Asignar empresa a un alumno (crear estancia)
     */
    public function asignarEmpresa(Request $request)
    {
        $validatedData = $request->validate([
            'id_alumno' => 'required|exists:alumno,id_alumno',
            'id_empresa' => 'required|exists:empresa,id_empresa',
            'id_tutor_empresa' => 'nullable|exists:users,id_usuario',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'horas_totales' => 'required|integer|min:1',
            'dias_totales' => 'required|integer|min:1',
        ]);
    
        $validatedData['id_tutor_centro'] = auth()->user()->id();

        $estancia = Estancia::create($validatedData);

        return response()->json([
            'message' => 'Empresa asignada con éxito',
            'estancia' => $estancia
        ], 201);
    }

    /**
     * Crear una nueva empresa
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'cif' => 'required|string|max:20|unique:empresa,cif',
            'nombre' => 'required|string|max:150',
            'poblacion' => 'required|string|max:100',
            'telefono' => 'required|string|max:20',
            'email' => 'required|email|max:100',
        ]);

        $empresa = Empresa::create($validatedData);

        return response()->json([
            'message' => 'Empresa creada con éxito',
            'empresa' => $empresa
        ], 201);
    }
}