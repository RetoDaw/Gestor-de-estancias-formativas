<?php
// app/Http/Controllers/EstanciaController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Estancia;
use App\Models\HorarioSemanal;
use App\Models\Horario;
use Illuminate\Support\Facades\DB;

class EstanciaController extends Controller
{
    /**
     * Obtiene el horario de un alumno específico
     * Si el usuario es tutor, debe pasar user_id
     * Si es alumno, usa su propio id
     */
    public function getHorarioAlumno(Request $request)
    {
        $user = $request->user();
        
        // Determinar qué alumno consultar
        $userId = $request->input('user_id');
        if (!$userId) {
            // Si no se pasa user_id, usar el usuario autenticado
            $userId = $user->id_usuario;
        }
        
        // Buscar la estancia activa del alumno
        $estancia = Estancia::with(['horariosSemales.horarios'])
            ->whereHas('alumno', function($query) use ($userId) {
                $query->where('id_alumno', $userId);
            })
            ->orderBy('fecha_inicio', 'desc')
            ->first();
        
        if (!$estancia) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró horario para este alumno'
            ], 404);
        }
        
        // Formatear la respuesta
        $horarioFormateado = $this->formatearHorario($estancia);
        
        return response()->json([
            'success' => true,
            'data' => $horarioFormateado
        ]);
    }
    
    /**
     * Crea un nuevo horario para un alumno
     * Solo tutor de centro
     */
    public function crearHorario(Request $request)
    {
        $user = $request->user();
        
        // Verificar que es tutor de centro
        if ($user->tipo_usuario !== 'TUTOR_CENTRO') {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para crear horarios'
            ], 403);
        }
        
        $validated = $request->validate([
            'id_alumno' => 'required|exists:alumno,id_alumno',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'horario' => 'required|array',
            'horario.*.dia' => 'required|in:Lunes,Martes,Miercoles,Jueves,Viernes',
            'horario.*.franjas' => 'required|array',
            'horario.*.franjas.*.hora_inicial' => 'required|integer|min:0|max:23',
            'horario.*.franjas.*.hora_final' => 'required|integer|min:0|max:23',
        ]);
        
        DB::beginTransaction();
        try {
            // Verificar si ya existe una estancia para este alumno
            $estanciaExistente = Estancia::whereHas('alumno', function($query) use ($validated) {
                $query->where('id_alumno', $validated['id_alumno']);
            })->first();
            
            if ($estanciaExistente) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Este alumno ya tiene un horario asignado. Use la opción de editar.'
                ], 400);
            }
            
            // Crear estancia (necesitarás los demás campos según tu lógica)
            $estancia = Estancia::create([
                'id_alumno' => $validated['id_alumno'],
                'id_empresa' => $request->input('id_empresa'),
                'id_tutor_centro' => $user->id_usuario,
                'fecha_inicio' => $validated['fecha_inicio'],
                'fecha_fin' => $validated['fecha_fin'],
                'horas_totales' => 500, 
                'dias_totales' => 90,  
            ]);
            
            // Crear horarios semanales
            foreach ($validated['horario'] as $diaData) {
                $horarioSemanal = HorarioSemanal::create([
                    'id_estancia' => $estancia->id_estancia,
                    'dia_semana' => $diaData['dia']
                ]);
                
                // Crear franjas horarias
                foreach ($diaData['franjas'] as $franja) {
                    Horario::create([
                        'id_horario_semanal' => $horarioSemanal->id_horario_semanal,
                        'hora_inicial' => $franja['hora_inicial'],
                        'hora_final' => $franja['hora_final']
                    ]);
                }
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Horario creado correctamente',
                'data' => $this->formatearHorario($estancia->fresh(['horariosSemales.horarios']))
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el horario: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Actualiza el horario existente
     * Solo tutor de centro
     */
    public function actualizarHorario(Request $request, $idEstancia)
    {
        $user = $request->user();
        
        // Verificar que es tutor de centro
        if ($user->tipo_usuario !== 'TUTOR_CENTRO') {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para editar horarios'
            ], 403);
        }
        
        $validated = $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'horario' => 'required|array',
            'horario.*.dia' => 'required|in:Lunes,Martes,Miercoles,Jueves,Viernes',
            'horario.*.franjas' => 'required|array',
            'horario.*.franjas.*.hora_inicial' => 'required|integer|min:0|max:23',
            'horario.*.franjas.*.hora_final' => 'required|integer|min:0|max:23',
        ]);
        
        $estancia = Estancia::find($idEstancia);
        
        if (!$estancia) {
            return response()->json([
                'success' => false,
                'message' => 'Estancia no encontrada'
            ], 404);
        }
        
        DB::beginTransaction();
        try {
            // Actualizar fechas
            $estancia->update([
                'fecha_inicio' => $validated['fecha_inicio'],
                'fecha_fin' => $validated['fecha_fin']
            ]);
            
            // Eliminar horarios antiguos
            foreach ($estancia->horariosSemales as $hs) {
                $hs->horarios()->delete();
                $hs->delete();
            }
            
            // Crear nuevos horarios
            foreach ($validated['horario'] as $diaData) {
                $horarioSemanal = HorarioSemanal::create([
                    'id_estancia' => $estancia->id_estancia,
                    'dia_semana' => $diaData['dia']
                ]);
                
                foreach ($diaData['franjas'] as $franja) {
                    Horario::create([
                        'id_horario_semanal' => $horarioSemanal->id_horario_semanal,
                        'hora_inicial' => $franja['hora_inicial'],
                        'hora_final' => $franja['hora_final']
                    ]);
                }
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Horario actualizado correctamente',
                'data' => $this->formatearHorario($estancia->fresh(['horariosSemales.horarios']))
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el horario: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Formatea el horario para el frontend
     */
    private function formatearHorario($estancia)
    {
        $horario = [];
        
        foreach ($estancia->horariosSemales as $hs) {
            $dia = strtolower($hs->dia_semana);
            $horario[$dia] = [
                'horaIni' => [],
                'horaFin' => []
            ];
            
            foreach ($hs->horarios as $h) {
                $horario[$dia]['horaIni'][] = $h->hora_inicial;
                $horario[$dia]['horaFin'][] = $h->hora_final;
            }
        }
        
        return [
            'id_estancia' => $estancia->id_estancia,
            'fecIni' => $estancia->fecha_inicio->format('d/m/Y'),
            'fecFin' => $estancia->fecha_fin->format('d/m/Y'),
            'horario' => $horario
        ];
    }
}