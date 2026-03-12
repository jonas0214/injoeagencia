<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewCommentNotification extends Notification
{
    use Queueable;

    protected $comment;
    protected $subtask;

    public function __construct($comment, $subtask)
    {
        $this->comment = $comment;
        $this->subtask = $subtask;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'new_comment',
            'subtask_id' => $this->subtask->id,
            'comment_id' => $this->comment->id,
            'title' => $this->subtask->title,
            'user_name' => $this->comment->user->name,
            'message' => "Nuevo mensaje de {$this->comment->user->name} en: {$this->subtask->title}",
            'content' => \Str::limit($this->comment->content, 50),
            'link' => route('projects.show', $this->subtask->task->project_id) . "?task_id=" . $this->subtask->id
        ];
    }
}
