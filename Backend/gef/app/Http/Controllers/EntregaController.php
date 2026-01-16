<?php

namespace App\Http\Controllers;

use App\Models\Entrega;
use App\Models\CuadernoPracticas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class EntregaController extends Controller
{
    /**
     * Crear una nueva entrega (solo tutores de centro)
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_grado' => 'required|exists:grado,id_grado'
        ]);

        $entrega = Entrega::create([
            'id_tutor' => $request->user()->id_usuario,
            'id_grado' => $request->id_grado
        ]);

        return response()->json([
            'message' => 'Entrega creada exitosamente',
            'entrega' => $entrega->load('grado')
        ], 201);
    }

    /**
     * Obtener entregas según el rol del usuario
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->tipo_usuario === 'ALUMNO') {
            // Obtener el id_grado del alumno desde la tabla alumno
            $alumno = DB::table('alumno')
                ->where('id_alumno', $user->id_usuario)
                ->first();

            if (!$alumno) {
                return response()->json([]);
            }

            // Obtener la estancia activa del alumno
            $estancia = DB::table('estancia')
                ->where('id_alumno', $user->id_usuario)
                ->whereDate('fecha_inicio', '<=', now())
                ->whereDate('fecha_fin', '>=', now())
                ->first();

            if (!$estancia) {
                return response()->json([]);
            }

            // Obtener IDs de cuadernos ya subidos por este alumno en esta estancia
            $cuadernosSubidos = DB::table('cuaderno_practicas')
                ->where('id_estancia', $estancia->id_estancia)
                ->pluck('id_entrega')
                ->toArray();

            // Obtener entregas del grado del alumno que aún no ha completado
            $entregas = DB::table('entrega')
                ->join('grado', 'entrega.id_grado', '=', 'grado.id_grado')
                ->where('entrega.id_grado', $alumno->id_grado)
                ->whereNotIn('entrega.id_entrega', $cuadernosSubidos)
                ->select(
                    'entrega.id_entrega',
                    'entrega.id_tutor',
                    'entrega.id_grado',
                    'entrega.created_at',
                    'entrega.updated_at',
                    'grado.nombre as grado_nombre',
                    'grado.id_grado as grado_id'
                )
                ->get()
                ->map(function($entrega) {
                    return [
                        'id_entrega' => $entrega->id_entrega,
                        'id_tutor' => $entrega->id_tutor,
                        'id_grado' => $entrega->id_grado,
                        'created_at' => $entrega->created_at,
                        'updated_at' => $entrega->updated_at,
                        'grado' => [
                            'id_grado' => $entrega->grado_id,
                            'nombre' => $entrega->grado_nombre
                        ]
                    ];
                });

            return response()->json($entregas);

        } else if ($user->tipo_usuario === 'TUTOR_CENTRO') {
            // El tutor ve todas las entregas
            $entregas = Entrega::with(['grado'])->get();
            return response()->json($entregas);
        } else {
            return response()->json(['message' => 'No autorizado'], 403);
        }
    }

    /**
     * Ver cuadernos entregados (con filtro opcional por grado)
     */
    public function verCuadernos(Request $request)
    {
        $query = DB::table('cuaderno_practicas')
            ->join('estancia', 'cuaderno_practicas.id_estancia', '=', 'estancia.id_estancia')
            ->join('alumno', 'estancia.id_alumno', '=', 'alumno.id_alumno')
            ->join('users', 'alumno.id_alumno', '=', 'users.id_usuario')
            ->join('entrega', 'cuaderno_practicas.id_entrega', '=', 'entrega.id_entrega')
            ->join('grado', 'entrega.id_grado', '=', 'grado.id_grado')
            ->select(
                'cuaderno_practicas.id_cuaderno',
                'users.nombre as alumno_nombre',
                'users.apellidos as alumno_apellidos',
                'grado.id_grado',
                'grado.nombre as grado_nombre',
                'cuaderno_practicas.archivo_pdf',
                'cuaderno_practicas.fecha_entrega',
                'cuaderno_practicas.id_estancia'
            );

        // Filtrar por grado si se proporciona
        if ($request->has('id_grado')) {
            $query->where('entrega.id_grado', $request->id_grado);
        }

        $cuadernos = $query->get()->map(function($cuaderno) {
            // Asegurarse de que la URL sea completa
            $archivoPdf = $cuaderno->archivo_pdf;
            if (!filter_var($archivoPdf, FILTER_VALIDATE_URL)) {
                // Si no es una URL completa, construirla
                $archivoPdf = url($archivoPdf);
            }
            
            return [
                'id' => $cuaderno->id_cuaderno,
                'alumno' => [
                    'nombre' => $cuaderno->alumno_nombre,
                    'apellidos' => $cuaderno->alumno_apellidos
                ],
                'grado' => [
                    'id' => $cuaderno->id_grado,
                    'nombre' => $cuaderno->grado_nombre
                ],
                'archivo_pdf' => $archivoPdf,
                'fecha_entrega' => $cuaderno->fecha_entrega,
                'id_estancia' => $cuaderno->id_estancia
            ];
        });

        return response()->json($cuadernos);
    }

    /**
     * Subir cuaderno (solo alumnos)
     */
    public function subirCuaderno(Request $request)
    {
        $request->validate([
            'id_entrega' => 'required|exists:entrega,id_entrega',
            'archivo' => 'required|file|mimes:pdf|max:10240' // 10MB max
        ]);

        $user = $request->user();

        // Verificar que el usuario es alumno
        if ($user->tipo_usuario !== 'ALUMNO') {
            return response()->json(['message' => 'Solo los alumnos pueden subir cuadernos'], 403);
        }

        // Obtener la estancia activa del alumno
        $estancia = DB::table('estancia')
            ->where('id_alumno', $user->id_usuario)
            ->whereDate('fecha_inicio', '<=', now())
            ->whereDate('fecha_fin', '>=', now())
            ->first();

        if (!$estancia) {
            return response()->json(['message' => 'No tienes una estancia activa'], 400);
        }

        // Verificar que no haya subido ya un cuaderno para esta entrega
        $cuadernoExistente = CuadernoPracticas::where('id_estancia', $estancia->id_estancia)
            ->where('id_entrega', $request->id_entrega)
            ->first();

        if ($cuadernoExistente) {
            return response()->json(['message' => 'Ya has subido un cuaderno para esta entrega'], 400);
        }

        // Guardar el archivo
        $archivo = $request->file('archivo');
        $nombreArchivo = time() . '_' . $user->id_usuario . '_' . $archivo->getClientOriginalName();
        $ruta = $archivo->storeAs('cuadernos', $nombreArchivo, 'public');

        // Generar URL completa del archivo
        $urlArchivo = url('storage/' . $ruta);

        // Crear el registro del cuaderno
        $cuaderno = CuadernoPracticas::create([
            'id_estancia' => $estancia->id_estancia,
            'id_entrega' => $request->id_entrega,
            'fecha_entrega' => now(),
            'archivo_pdf' => $urlArchivo
        ]);

        return response()->json([
            'message' => 'Cuaderno subido exitosamente',
            'cuaderno' => $cuaderno
        ], 201);
    }
}