<?php

namespace Database\Seeders;

use App\Models\Cancha;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@canchaselvirrey.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Recepción',
            'email' => 'recepcion@canchaselvirrey.com',
            'password' => Hash::make('password'),
            'role' => 'recepcion',
        ]);

        User::create([
            'name' => 'Cliente de Prueba',
            'email' => 'cliente@canchaselvirrey.com',
            'password' => Hash::make('password'),
            'role' => 'cliente',
        ]);

        Cancha::insert([
            ['nombre' => 'Cancha 1 - Fútbol', 'tipo' => 'futbol', 'capacidad' => 10, 'precio_hora' => 80, 'estado' => 'disponible', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Cancha 2 - Básquet', 'tipo' => 'basquet', 'capacidad' => 10, 'precio_hora' => 60, 'estado' => 'disponible', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Cancha 3 - Vóley', 'tipo' => 'voley', 'capacidad' => 12, 'precio_hora' => 50, 'estado' => 'disponible', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
