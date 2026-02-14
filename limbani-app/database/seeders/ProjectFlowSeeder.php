<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;

class ProjectFlowSeeder extends Seeder
{
    public function run(): void
    {
        // Buscamos el primer proyecto (ID 1 o 2 de Dentus/Carolina)
        $project = Project::first();
        $project = \App\Models\Project::where('name', 'JONATHAN')->first();

        if ($project) {
            // Creamos la sección estratégica
            $section = $project->tasks()->create([
                'title' => 'ESTRATEGIA Y BRIEF',
                'status' => 'activo'
            ]);

            // Añadimos las acciones concretas de tu flujo
            $section->subtasks()->createMany([
                ['title' => 'BRIEF MENSUAL DE PROGRAMACIÓN', 'due_date' => '2026-01-15'],
                ['title' => 'RECEPCIÓN DE BRIEF MENSUAL', 'due_date' => '2026-01-20'],
                ['title' => 'REALIZAR ESTRATEGIA DE CONTENIDOS', 'due_date' => '2026-01-24'],
                ['title' => 'REUNIÓN SOCIALIZACIÓN ESTRATEGIA', 'due_date' => now()],
            ]);
        }
    }
}