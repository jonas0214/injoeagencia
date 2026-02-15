<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subtask extends Model
{
    // Campos que permitimos guardar masivamente
    protected $fillable = [
        'title',
        'description',
        'due_date',
        'is_completed',
        'task_id',
        'parent_id',
        'team_member_id',
        'ai_suggestion'
    ];

    /**
     * Convertir campos automáticamente a tipos nativos.
     */
    protected $casts = [
        'due_date' => 'datetime',
        'is_completed' => 'boolean',
    ];

    /**
     * Una subtarea pertenece a una tarea (Categoría).
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Relación recursiva: Hijos (Subtareas de esta subtarea).
     * Esto permite el "Tree Structure" infinito de Asana.
     */
    public function children()
    {
        return $this->hasMany(Subtask::class, 'parent_id');
    }

    /**
     * Relación recursiva: Padre.
     */
    public function parent()
    {
        return $this->belongsTo(Subtask::class, 'parent_id');
    }

    /**
     * Comentarios de la subtarea.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->latest();
    }

    public function teamMember(): BelongsTo
    {
        return $this->belongsTo(TeamMember::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }
}