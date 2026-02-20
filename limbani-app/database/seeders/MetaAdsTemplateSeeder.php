<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\User;

class MetaAdsTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('role', 'admin')->first() ?? User::first();

        if (!$user) return;

        // Crear el proyecto de plantilla
        $template = Project::create([
            'name' => 'PLANTILLA: Programación Meta Ads',
            'description' => 'Estructura base para campañas de Facebook e Instagram Ads.',
            'user_id' => $user->id,
            'status' => 'activo',
            'is_template' => true
        ]);

        // SECCIÓN 1: SETUP Y ACCESOS
        $setup = $template->tasks()->create(['title' => '1. SETUP Y ACCESOS', 'position' => 0]);
        $setup->subtasks()->createMany([
            ['title' => 'Verificar acceso a Business Manager', 'position' => 0],
            ['title' => 'Instalación/Verificación de Píxel de Meta', 'position' => 1],
            ['title' => 'Configuración de API de Conversiones', 'position' => 2],
            ['title' => 'Verificación de Dominio en Meta', 'position' => 3],
        ]);

        // SECCIÓN 2: ESTRATEGIA Y AUDIENCIAS
        $strategy = $template->tasks()->create(['title' => '2. ESTRATEGIA Y AUDIENCIAS', 'position' => 1]);
        $strategy->subtasks()->createMany([
            ['title' => 'Definición de KPI y Objetivos', 'position' => 0],
            ['title' => 'Investigación de Buyer Persona', 'position' => 1],
            ['title' => 'Creación de Audiencias (Lookalike, Custom, Intereses)', 'position' => 2],
        ]);

        // SECCIÓN 3: PRODUCCIÓN CREATIVA
        $creative = $template->tasks()->create(['title' => '3. PRODUCCIÓN CREATIVA', 'position' => 2]);
        $creative->subtasks()->createMany([
            ['title' => 'Diseño de Gráficos / Edición de Video', 'position' => 0],
            ['title' => 'Redacción de Copywriting (Ad Copy)', 'position' => 1],
            ['title' => 'Creación de Formularios (si aplica)', 'position' => 2],
        ]);

        // SECCIÓN 4: IMPLEMENTACIÓN Y LANZAMIENTO
        $launch = $template->tasks()->create(['title' => '4. IMPLEMENTACIÓN Y LANZAMIENTO', 'position' => 3]);
        $launch->subtasks()->createMany([
            ['title' => 'Montaje de Campaña en Ads Manager', 'position' => 0],
            ['title' => 'Configuración de Reglas Automatizadas', 'position' => 1],
            ['title' => 'Revisión final de enlaces y creativos', 'position' => 2],
            ['title' => 'Lanzamiento de Campaña', 'position' => 3],
        ]);

        // SECCIÓN 5: OPTIMIZACIÓN Y REPORTING
        $reporting = $template->tasks()->create(['title' => '5. OPTIMIZACIÓN Y REPORTING', 'position' => 4]);
        $reporting->subtasks()->createMany([
            ['title' => 'Seguimiento de primeras 48-72 horas', 'position' => 0],
            ['title' => 'A/B Testing de Creativos', 'position' => 1],
            ['title' => 'Ajuste de Pujas y Presupuesto', 'position' => 2],
            ['title' => 'Entrega de Reporte Mensual de Resultados', 'position' => 3],
        ]);
    }
}
