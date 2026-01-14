<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ResultadoAprendizajeController;
use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\UsuarioController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/guardarRA', [ResultadoAprendizajeController::class, 'store']);
    Route::post('/guardarAlumno', [AlumnoController::class, 'store']);
    Route::post('/guardarEmpresa', [EmpresaController::class, 'store']);
    Route::post('/guardarUsuario', [UsuarioController::class, 'store']);
});
