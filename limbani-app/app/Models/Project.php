<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = ['name', 'description', 'logo', 'user_id', 'status', 'is_template'];

    public function tasks()
    {
        return $this->hasMany(Task::class)->orderBy('position');
    }

    public function brief()
    {
        return $this->hasOne(Brief::class);
    }

    /**
     * Get or create brief for this project
     */
    public function getOrCreateBrief(): Brief
    {
        return $this->brief()->firstOrCreate([], [
            'answers' => array_fill_keys(array_map(fn($i) => "q$i", range(1, 20)), '')
        ]);
    }
}