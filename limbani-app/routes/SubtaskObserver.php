<?php

namespace App\Observers;

use App\Models\Subtask;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SubtaskObserver
{
    /**
     * Se ejecuta automáticamente cuando se CREA una subtarea.
     */
    public function created(Subtask $subtask): void
    {
        // URL de tu Webhook en n8n (La obtendrás al crear el nodo "Webhook" en n8n)
        // Por ahora pon una temporal o déjala así hasta que configures n8n.
        $n8nWebhookUrl = 'https://tu-n8n-hostinger.com/webhook/analizar-subtarea';

        try {
            // Enviamos los datos relevantes a n8n
            Http::timeout(2)->post($n8nWebhookUrl, [
                'subtask_id' => $subtask->id,
                'title' => $subtask->title,
                'description' => $subtask->description,
                'created_at' => $subtask->created_at->toDateTimeString(),
                // Información de contexto (útil para la IA)
                'parent_task' => $subtask->task->title ?? 'Sin categoría',
            ]);
        } catch (\Exception $e) {
            // Si n8n falla, no queremos que falle la app, solo registramos el error
            Log::error('Error enviando subtarea a n8n: ' . $e->getMessage());
        }
    }
}