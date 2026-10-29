<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Checklist automatizado mínimo: después de levantar el sistema
 * (contenedor o servidor local), estas rutas base deben responder,
 * confirmando que la aplicación arrancó correctamente de punta a punta.
 */
class SmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_pagina_de_inicio_responde(): void
    {
        $this->get('/')->assertStatus(200);
    }

    public function test_la_pantalla_de_login_responde(): void
    {
        $this->get('/login')->assertStatus(200);
    }

    public function test_el_endpoint_de_salud_de_laravel_responde(): void
    {
        $this->get('/up')->assertStatus(200);
    }

    public function test_el_endpoint_publico_de_canchas_responde(): void
    {
        $this->getJson('/api/canchas')->assertStatus(200);
    }

    /**
     * El cliente de pruebas de Laravel solo despacha a rutas definidas en
     * el router, no sirve archivos estáticos de /public como lo haría un
     * servidor real (Apache/php artisan serve). Por eso verificamos
     * directamente que el archivo de la PWA exista en el build, en vez
     * de pedirle al cliente de pruebas que lo "descargue".
     */
    public function test_el_archivo_de_la_pwa_existe_en_public(): void
    {
        $this->assertFileExists(public_path('pwa/index.html'));
        $this->assertFileExists(public_path('pwa/manifest.json'));
    }
}
