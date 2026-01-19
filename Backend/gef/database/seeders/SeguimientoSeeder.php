<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Estancia;
use App\Models\User;
use App\Models\Seguimiento;
use Carbon\Carbon;

class SeguimientoSeeder extends Seeder
{
    public function run(): void
    {
        $estancias = Estancia::all();
        $usuarios = User::all();

        if ($estancias->isEmpty() || $usuarios->isEmpty()) {
            echo "⚠️  No hay estancias o usuarios disponibles. Ejecuta primero los seeders de Usuarios y Estancias.\n";
            return;
        }

        $accionesEjemplo = [
            "Llamada de seguimiento",
            "Correo enviado",
            "Reunión presencial",
            "Videollamada de revisión",
            "Mensaje de aviso"
        ];

        $medios = ["EMAIL", "EN_PERSONA", "TELEFONO", "VIDEOLLAMADA", "OTRO"];

        foreach ($estancias as $estancia) {
            $numSeguimientos = rand(3,5);

            for ($i = 0; $i < $numSeguimientos; $i++) {
                $emisor = $usuarios->random();
                do {
                    $receptor = $usuarios->random();
                } while ($receptor->id_usuario === $emisor->id_usuario);

                $fechaInicio = Carbon::parse($estancia->fecha_inicio);
                $fechaFin = Carbon::parse($estancia->fecha_fin);
                $diaAleatorio = Carbon::createFromTimestamp(rand($fechaInicio->timestamp, $fechaFin->timestamp));

                $horaAleatoria = Carbon::createFromTime(rand(9,16), rand(0,59))->format('H:i:s');

                Seguimiento::create([
                    'id_estancia' => $estancia->id_estancia,
                    'dia' => $diaAleatorio->format('Y-m-d'),
                    'hora' => $horaAleatoria,
                    'accion' => $accionesEjemplo[array_rand($accionesEjemplo)],
                    'id_emisor' => $emisor->id_usuario,
                    'id_receptor' => $receptor->id_usuario,
                    'medio' => $medios[array_rand($medios)],
                ]);
            }

            echo "   ✓ Creado $numSeguimientos seguimientos para estancia ID {$estancia->id_estancia}\n";
        }

        echo "✅ Seguimientos creados: " . Seguimiento::count() . "\n";
    }
}
