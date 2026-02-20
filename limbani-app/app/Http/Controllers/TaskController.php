<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function store(Request $request, Project $project)
    {
        \Log::info("Creando Nueva Sección para Proyecto ID: " . $project->id . " Titulo: " . $request->title);
        
        // Validamos los datos de la tarea (Sección)
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Creamos la tarea vinculada a este proyecto
        $section = $project->tasks()->create([
            'title' => $request->title,
            'description' => $request->description,
            'status' => 'pendiente',
            'position' => $project->tasks()->count(),
        ]);

        if ($request->wantsJson()) {
            return response()->json($section);
        }

        return redirect()->route('projects.show', $project)->with('success', 'Sección añadida correctamente.');
    }

    // Actualizar nombre de la lista
    public function update(Request $request, Task $task)
    {
        $request->validate(['title' => 'required|string|max:255']);
        $task->update(['title' => $request->title]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'task' => $task]);
        }
        
        return back()->with('success', 'Lista actualizada.');
    }

    // Eliminar lista completa (Sección)
    public function destroy(Request $request, Task $task)
    {
        \Log::info("Eliminando Task (Sección) ID: " . $task->id);
        $task->delete();

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Lista eliminada.');
    }

    public function move(Request $request, Task $task)
    {
        $request->validate([
            'direction' => 'required|in:up,down',
        ]);

        $project = $task->project;
        $tasks = $project->tasks()->get();
        $currentIndex = $tasks->search(fn($t) => $t->id === $task->id);

        if ($request->direction === 'up' && $currentIndex > 0) {
            $prevTask = $tasks[$currentIndex - 1];
            $task->update(['position' => $prevTask->position - 1]);
        } elseif ($request->direction === 'down' && $currentIndex < $tasks->count() - 1) {
            $nextTask = $tasks[$currentIndex + 1];
            $task->update(['position' => $nextTask->position + 1]);
        }

        // Re-normalizar posiciones para evitar números gigantes o negativos
        $project->tasks()->get()->each(function($t, $i) {
            $t->update(['position' => $i]);
        });

        return back()->with('success', 'Sección movida.');
    }

    // Duplicar lista con sus subtareas
    public function duplicate(Task $task)
    {
        $newTask = $task->replicate();
        $newTask->title = $task->title . ' (Copia)';
        $newTask->save();

        // Mapa para mantener la relación de parentesco en la nueva sección
        $map = [];

        // Primero duplicamos todas las subtareas sin parent_id para tener los nuevos padres
        foreach ($task->subtasks()->whereNull('parent_id')->get() as $subtask) {
            $newSubtask = $this->cloneSubtask($subtask, $newTask->id);
            $map[$subtask->id] = $newSubtask->id;
            
            // Luego duplicamos recursivamente los hijos
            $this->duplicateChildren($subtask, $newSubtask, $newTask->id, $map);
        }

        return back()->with('success', 'Lista duplicada correctamente.');
    }

    private function duplicateChildren($oldParent, $newParent, $taskId, &$map)
    {
        foreach ($oldParent->children as $child) {
            $newChild = $this->cloneSubtask($child, $taskId);
            $newChild->parent_id = $newParent->id;
            $newChild->save();
            $map[$child->id] = $newChild->id;
            
            $this->duplicateChildren($child, $newChild, $taskId, $map);
        }
    }

    private function cloneSubtask($subtask, $taskId)
    {
        $newSubtask = $subtask->replicate();
        $newSubtask->task_id = $taskId;
        $newSubtask->parent_id = null; // Se asignará después si es necesario
        
        // Limpiar responsable y fechas según requerimiento
        $newSubtask->team_member_id = null;
        $newSubtask->due_date = null;
        $newSubtask->is_completed = false;
        $newSubtask->is_approved = false;
        $newSubtask->approved_at = null;
        $newSubtask->approved_by = null;
        
        $newSubtask->save();
        return $newSubtask;
    }
}