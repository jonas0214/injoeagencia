<?php

namespace App\Jobs;

use App\Models\Subtask;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendSubtaskToN8n implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $subtask;

    /**
     * Create a new job instance.
     */
    public function __construct(Subtask $subtask)
    {
        $this->subtask = $subtask;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $n8nWebhookUrl = 'https://n8n.srv1317921.hstgr.cloud/webhook-test/analizar-subtarea';

        try {
            $teamMember = $this->subtask->teamMember;
            $photoUrl = $teamMember && $teamMember->photo 
                ? config('app.url') . '/storage/' . $teamMember->photo 
                : null;

            Http::timeout(10)->post($n8nWebhookUrl, [
                'subtask_id' => $this->subtask->id,
                'title' => $this->subtask->title,
                'description' => $this->subtask->description,
                'created_at' => $this->subtask->created_at->toDateTimeString(),
                'team_member' => [
                    'name' => $teamMember->name ?? 'Sin asignar',
                    'email' => $teamMember->email ?? null,
                    'photo_url' => $photoUrl,
                ],
                'parent_task' => $this->subtask->task->title ?? 'Sin categoría',
            ]);
        } catch (\Exception $e) {
            Log::error('Error enviando subtarea a n8n (Job): ' . $e->getMessage());
            // Re-lanzamos para que la cola lo reintente si es necesario
            throw $e;
        }
    }
}
