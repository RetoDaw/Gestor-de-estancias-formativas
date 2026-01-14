<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Estancia;
use App\Models\Alumno;
use App\Models\Empresa;
use App\Models\User;
use App\Models\HorarioSemanal;
use App\Models\Horario;
use Carbon\Carbon;

class EstanciaSeeder extends Seeder
{
    /**
     * Crea estancias de prácticas con horarios
     */
    public function run(): void
    {
        $alumnos = Alumno::all();
        $empresas = Empresa::all();
        $tutoresCentro = User::where('tipo_usuario', 'TUTOR_CENTRO')->get();
        $tutoresEmpresa = User::where('tipo_usuario', 'TUTOR_EMPRESA')->get();

        if ($alumnos->isEmpty() || $empresas->isEmpty()) {
            echo "⚠️  Faltan alumnos o empresas. Ejecuta los seeders anteriores.\n";
            return;
        }

        // Crear 6 estancias de ejemplo
        $estanciasData = [
            // Estancia ACTIVA
            [
                'fecha_inicio' => Carbon::now()->subDays(20),
                'fecha_fin' => Carbon::now()->addDays(40),
                'estado' => 'ACTIVA'
            ],
            // Estancia ACTIVA
            [
                'fecha_inicio' => Carbon::now()->subDays(15),
                'fecha_fin' => Carbon::now()->addDays(45),
                'estado' => 'ACTIVA'
            ],
            // Estancia FINALIZADA
            [
                'fecha_inicio' => Carbon::now()->subDays(80),
                'fecha_fin' => Carbon::now()->subDays(20),
                'estado' => 'FINALIZADA'
            ],
            // Estancia FINALIZADA
            [
                'fecha_inicio' => Carbon::now()->subDays(70),
                'fecha_fin' => Carbon::now()->subDays(10),
                'estado' => 'FINALIZADA'
            ],
            // Estancia PRÓXIMA
            [
                'fecha_inicio' => Carbon::now()->addDays(10),
                'fecha_fin' => Carbon::now()->addDays(70),
                'estado' => 'PROXIMA'
            ],
            // Estancia PRÓXIMA
            [
                'fecha_inicio' => Carbon::now()->addDays(15),
                'fecha_fin' => Carbon::now()->addDays(75),
                'estado' => 'PROXIMA'
            ],
        ];

        foreach ($estanciasData as $index => $data) {
            $alumno = $alumnos[$index % $alumnos->count()];
            $empresa = $empresas[$index % $empresas->count()];
            $tutorCentro = $tutoresCentro[$index % $tutoresCentro->count()];
            $tutorEmpresa = $tutoresEmpresa[$index % $tutoresEmpresa->count()];

            $diasTotales = $data['fecha_inicio']->diffInDays($data['fecha_fin']);
            $horasTotales = $diasTotales * 7; // Aprox 7 horas/día

            $estancia = Estancia::create([
                'id_alumno' => $alumno->id_alumno,
                'id_empresa' => $empresa->id_empresa,
                'id_tutor_empresa' => $tutorEmpresa->id_usuario,
                'id_tutor_centro' => $tutorCentro->id_usuario,
                'fecha_inicio' => $data['fecha_inicio'],
                'fecha_fin' => $data['fecha_fin'],
                'horas_totales' => $horasTotales,
                'dias_totales' => $diasTotales
            ]);

            // Crear horario semanal típico (Lunes a Viernes, 9-14 y 15-17)
            $dias = ['Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes'];
            
            foreach ($dias as $dia) {
                $horarioSemanal = HorarioSemanal::create([
                    'id_estancia' => $estancia->id_estancia,
                    'dia_semana' => $dia
                ]);

                // Franja mañana: 9-14
                Horario::create([
                    'id_horario_semanal' => $horarioSemanal->id_horario_semanal,
                    'hora_inicial' => 9,
                    'hora_final' => 14
                ]);

                // Franja tarde: 15-17
                Horario::create([
                    'id_horario_semanal' => $horarioSemanal->id_horario_semanal,
                    'hora_inicial' => 15,
                    'hora_final' => 17
                ]);
            }

            echo "   ✓ Estancia {$data['estado']}: {$alumno->usuario->nombre} en {$empresa->nombre}\n";
        }

        echo "✅ Creadas " . Estancia::count() . " estancias\n";
        echo "✅ Creados " . HorarioSemanal::count() . " días de horario\n";
        echo "✅ Creadas " . Horario::count() . " franjas horarias\n";
    }
}