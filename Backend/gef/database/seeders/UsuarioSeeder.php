<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    /**
     * Crea usuarios de prueba: alumnos, tutores de centro y tutores de empresa
     */
    public function run(): void
    {
        // Tutores de Centro
        User::create([
            'email' => 'juan.tutor@centro.edu',
            'password_hash' => Hash::make('password123'),
            'nombre' => 'Juan',
            'apellidos' => 'García López',
            'telefono' => '943123456',
            'tipo_usuario' => 'TUTOR_CENTRO'
        ]);

        User::create([
            'email' => 'maria.tutora@centro.edu',
            'password_hash' => Hash::make('password123'),
            'nombre' => 'María',
            'apellidos' => 'Fernández Ruiz',
            'telefono' => '943123457',
            'tipo_usuario' => 'TUTOR_CENTRO'
        ]);

        // Tutores de Empresa
        User::create([
            'email' => 'carlos.tech@empresa.com',
            'password_hash' => Hash::make('password123'),
            'nombre' => 'Carlos',
            'apellidos' => 'Martínez Sánchez',
            'telefono' => '943234567',
            'tipo_usuario' => 'TUTOR_EMPRESA'
        ]);

        User::create([
            'email' => 'laura.dev@empresa.com',
            'password_hash' => Hash::make('password123'),
            'nombre' => 'Laura',
            'apellidos' => 'Rodríguez Torres',
            'telefono' => '943234568',
            'tipo_usuario' => 'TUTOR_EMPRESA'
        ]);

        // Alumnos
        $alumnos = [
            ['Pedro', 'Jiménez Moreno', 'pedro.jimenez@alumno.edu'],
            ['Ana', 'López Díaz', 'ana.lopez@alumno.edu'],
            ['Miguel', 'Sánchez Ruiz', 'miguel.sanchez@alumno.edu'],
            ['Carmen', 'González Pérez', 'carmen.gonzalez@alumno.edu'],
            ['David', 'Martín Gómez', 'david.martin@alumno.edu'],
            ['Sara', 'Hernández Castro', 'sara.hernandez@alumno.edu'],
            ['Javier', 'Díaz Romero', 'javier.diaz@alumno.edu'],
            ['Elena', 'Ruiz Navarro', 'elena.ruiz@alumno.edu'],
        ];

        foreach ($alumnos as $alumno) {
            User::create([
                'email' => $alumno[2],
                'password_hash' => Hash::make('password123'),
                'nombre' => $alumno[0],
                'apellidos' => $alumno[1],
                'telefono' => '943' . rand(300000, 399999),
                'tipo_usuario' => 'ALUMNO'
            ]);
        }

        echo "✅ Creados " . User::count() . " usuarios\n";
    }
}