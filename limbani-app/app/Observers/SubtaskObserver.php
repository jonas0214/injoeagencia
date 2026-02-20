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
        // REEMPLAZA ESTO con tu URL real de n8n (ej: https://n8n.tudominio.com/webhook-test/...)
        // Si usas la URL de "Test" de n8n, asegúrate de tener el workflow en modo "Listening".
        $n8nWebhookUrl = 'https://n8n.srv1317921.hstgr.cloud/webhook-test/analizar-subtarea'; // <--- PEGA TU URL AQUÍ

        try {
            $teamMember = $subtask->teamMember;
            $photoUrl = $teamMember && $teamMember->photo
                ? config('app.url') . '/storage/' . $teamMember->photo
                : null;

            // Enviamos los datos relevantes a n8n
            Http::timeout(5)->post($n8nWebhookUrl, [
                'subtask_id' => $subtask->id,
                'title' => $subtask->title,
                'description' => $subtask->description,
                'created_at' => $subtask->created_at->toDateTimeString(),
                'team_member' => [
                    'name' => $teamMember->name ?? 'Sin asignar',
                    'email' => $teamMember->email ?? null,
                    'photo_url' => $photoUrl,
                ],
                // Información de contexto (útil para la IA)
                'parent_task' => $subtask->task->title ?? 'Sin categoría',
            ]);
        } catch (\Exception $e) {
            // Si n8n falla, no queremos que falle la app, solo registramos el error
            Log::error('Error enviando subtarea a n8n: ' . $e->getMessage());
        }
    }
}