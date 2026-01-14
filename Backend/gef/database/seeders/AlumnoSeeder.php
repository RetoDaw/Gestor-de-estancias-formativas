<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Alumno;
use App\Models\Grado;

class AlumnoSeeder extends Seeder
{
    /**
     * Vincula usuarios tipo ALUMNO con grados
     */
    public function run(): void
    {
        $usuariosAlumnos = User::where('tipo_usuario', 'ALUMNO')->get();
        $grados = Grado::all();

        if ($grados->isEmpty()) {
            echo "⚠️  No hay grados creados. Ejecuta GradoSeeder primero.\n";
            return;
        }

        foreach ($usuariosAlumnos as $index => $usuario) {
            // Distribuir alumnos entre los grados disponibles
            $grado = $grados[$index % $grados->count()];

            Alumno::create([
                'id_alumno' => $usuario->id_usuario,
                'id_grado' => $grado->id_grado
            ]);
        }

        echo "✅ Creados " . Alumno::count() . " alumnos\n";
        
        // Mostrar distribución
        foreach ($grados as $grado) {
            $count = Alumno::where('id_grado', $grado->id_grado)->count();
            echo "   → {$grado->nombre}: {$count} alumnos\n";
        }
    }
}