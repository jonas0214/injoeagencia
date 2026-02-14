<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = ['title', 'description', 'status', 'project_id'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // ESTA ES LA FUNCIÓN QUE TE FALTA Y CAUSA EL ERROR 500
    public function subtasks()
    {
        return $this->hasMany(Subtask::class);
    }
}