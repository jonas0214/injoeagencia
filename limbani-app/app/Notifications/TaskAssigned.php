<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskAssigned extends Notification
{
    use Queueable;

    protected $subtask;
    protected $assignedBy;

    public function __construct($subtask, $assignedBy)
    {
        $this->subtask = $subtask;
        $this->assignedBy = $assignedBy;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'task_assigned',
            'subtask_id' => $this->subtask->id,
            'title' => $this->subtask->title,
            'project_name' => $this->subtask->task->project->name ?? 'Sin proyecto',
            'assigned_by_name' => $this->assignedBy->name,
            'message' => "Te han asignado la tarea: {$this->subtask->title}",
            'link' => route('projects.show', $this->subtask->task->project_id) . "?task_id=" . $this->subtask->id
        ];
    }
}
