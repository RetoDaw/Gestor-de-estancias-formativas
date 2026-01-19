<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empresa;

class EmpresaController extends Controller
{
    public function index(){
        $empresas = Empresa::all();
        return response()->json($empresas);
    }
    public function store(Request $request){
        $validatedData = $request->validate([
            'email' => 'required|email|unique:empresa,email',
            'nombre_empresa' => 'required|string|max:100',
            'telefono' => 'required|string|max:14',
            'cif' => 'required|string|max:9|unique:empresa,cif',
            'poblacion' => 'required|string|max:100'
        ]);
        $empresa = Empresa::create([
            'email' => $validatedData['email'],
            'nombre' => $validatedData['nombre_empresa'],
            'telefono' => $validatedData['telefono'],
            'cif' => $validatedData['cif'],
            'poblacion' => $validatedData['poblacion'],
        ]);
        return response()->json(['message' => 'Empresa creada con exito'], 201);
    }
}
