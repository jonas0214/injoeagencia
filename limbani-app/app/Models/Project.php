<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = ['name', 'description', 'logo', 'user_id', 'status', 'is_template', 'position', 'category'];

    // Categorías de Proyectos
    const CAT_AGENCIA = 'agencia';
    
    // 1. DIRECCIÓN GENERAL
    const CAT_CEO = 'ceo_direccion';
    
    // 2. OPERACIÓN & PRODUCCIÓN
    const CAT_PROD_AV = 'produccion_av';
    const CAT_POSTPROD = 'postproduccion';
    const CAT_DISENO = 'diseno_grafico';
    const CAT_WEB = 'desarrollo_web';
    
    // 3. ADMINISTRACIÓN & TALENTO HUMANO
    const CAT_RRHH = 'rrhh';
    const CAT_ADMIN = 'direccion_admin';

    /**
     * Scope para filtrar por categoría
     */
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class)->orderBy('position');
    }

    public function brief()
    {
        return $this->hasOne(Brief::class);
    }

    public function subtasks()
    {
        return $this->hasManyThrough(Subtask::class, Task::class);
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