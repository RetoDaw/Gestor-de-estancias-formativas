<?php

namespace App\Http\Controllers;

use App\Models\Grado;
use Illuminate\Http\Request;

class GradoController extends Controller
{
    /**
     * Listar todos los grados
     */
    public function index()
    {
        $grados = Grado::all();
        return response()->json($grados);
    }
}