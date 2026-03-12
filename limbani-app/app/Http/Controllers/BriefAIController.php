<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class BriefAIController extends Controller
{
    /**
     * Obtener sugerencias de IA para un campo específico del Brief.
     */
    public function getSuggestions(Request $request, Project $project)
    {
        $questionId = $request->input('question_id');
        $sections = $project->tasks->pluck('title')->toArray();
        $projectName = $project->name;

        $suggestions = $this->generateSuggestions($questionId, $projectName, $sections);

        return response()->json([
            'success' => true,
            'suggestions' => $suggestions
        ]);
    }

    /**
     * Lógica de "Simulación de IA" basada en patrones estratégicos de agencia.
     */
    private function generateSuggestions($qId, $projectName, $sections)
    {
        // Si hay secciones creadas, las usamos para dar contexto
        $sectionContext = count($sections) > 0 ? implode(', ', $sections) : 'General';

        $patterns = [
            'q3' => [ // Objetivo Principal
                "Posicionar a $projectName como referente en " . ($sections[0] ?? 'el mercado') . " durante este mes.",
                "Aumentar la captación de leads cualificados interesados en $sectionContext.",
                "Consolidar la identidad digital y el engagement de la comunidad en torno a " . ($sections[1] ?? 'nuestros valores') . "."
            ],
            'q4' => [ // Mensaje Principal
                "Confianza y profesionalismo en cada detalle de $projectName.",
                "Innovación constante: Descubre lo que viene en nuestra campaña de $sectionContext.",
                "Soluciones reales para tu día a día con el respaldo de Injoe Agencia."
            ],
            'q6' => [ // Productos a destacar
                "Enfoque total en el lanzamiento de " . ($sections[0] ?? 'nuestro servicio estrella') . ".",
                "Promoción especial de fidelización para clientes antiguos.",
                "Pack estratégico combinando " . ($sections[0] ?? 'Producto A') . " y " . ($sections[1] ?? 'Producto B') . "."
            ],
            'q19' => [ // Resultado Ideal
                "Duplicar el alcance orgánico comparado con el mes anterior.",
                "Lograr una tasa de conversión del 5% en los anuncios de $sectionContext.",
                "Generar al menos 20 nuevas consultas directas vía WhatsApp por semana."
            ],
            'q20' => [ // Observaciones
                "Coordinar sesión de fotos para el material de " . ($sections[0] ?? 'la nueva campaña') . ".",
                "Priorizar contenido en formato Reels para maximizar el alcance.",
                "Revisar métricas a mitad de mes para ajustar la inversión en pauta."
            ]
        ];

        // Si no tenemos patrón, damos unas genéricas
        return $patterns[$qId] ?? [
            "Optimizar la comunicación en canales digitales.",
            "Reforzar el branding de $projectName.",
            "Mejorar la retención de clientes actuales."
        ];
    }
}
