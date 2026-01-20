<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ResultadoAprendizajeController;
use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\EntregaController;
use App\Http\Controllers\GradoController;
use App\Http\Controllers\CompetenciaTecnicaController;
use App\Http\Controllers\SeguimientoController;
use App\Http\Controllers\EstanciaController;


Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function () {
    
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('/usuario/me', [UsuarioController::class, 'getAuthUser']);
    
    Route::get('/esTutorCentro', [UsuarioController::class, 'esTutorCentro']);
    Route::get('/esAlumno', [UsuarioController::class, 'esAlumno']);
    
    Route::get('/grados-con-alumnos', [UsuarioController::class, 'listarGradosConAlumnos']);
    Route::get('/alumnos-por-grado', [UsuarioController::class, 'listarAlumnosPorGrado']);
    Route::get('/alumno', [UsuarioController::class, 'getAlumno']);
    Route::post('/guardarAlumno', [AlumnoController::class, 'store']);
    
    Route::get('/buscarUsuario', [UsuarioController::class, 'search']);
    Route::post('/guardarUsuario', [UsuarioController::class, 'store']);
    
    Route::get('/grados', [GradoController::class, 'index']);
    
    Route::post('/guardarRA', [ResultadoAprendizajeController::class, 'store']);
    
    Route::post('/guardarCompetencia', [CompetenciaTecnicaController::class, 'store']);
    
    Route::post('/entregas', [EntregaController::class, 'store']);
    Route::get('/entregas', [EntregaController::class, 'index']);
    Route::get('/cuadernos', [EntregaController::class, 'verCuadernos']);
    Route::post('/cuadernos', [EntregaController::class, 'subirCuaderno']);
    Route::post('/asignar-empresa', [EmpresaController::class, 'asignarEmpresa']);
    Route::post('/guardarEmpresa', [EmpresaController::class, 'store']);
    Route::get('/empresas', [EmpresaController::class, 'index']);
    Route::get('/empresa_alumno', [EmpresaController::class, 'getEmpresaAlumno']);    
    Route::get('/mostrarSeguimientos', [SeguimientoController::class, 'index']);
    Route::get('/seguimientos/{id}', [SeguimientoController::class, 'show']);
    Route::post('/guardarSeguimiento', [SeguimientoController::class, 'store']);
    Route::delete('/seguimientos/{id}', [SeguimientoController::class, 'delete']);

    // Horario/Calendario
    Route::get('/horario-alumno', [EstanciaController::class, 'getHorarioAlumno']);
    Route::post('/horario', [EstanciaController::class, 'crearHorario']);
    Route::put('/horario/{idEstancia}', [EstanciaController::class, 'actualizarHorario']);

    Route::get(
        '/alumnos/{idAlumno}/asignaturas/{idAsignatura}/nota-final',
        [AlumnoController::class, 'notaFinal']
    );
    Route::get(
        '/alumnos/{idAlumno}/notas',
        [AlumnoController::class, 'getNotas']
    );
});