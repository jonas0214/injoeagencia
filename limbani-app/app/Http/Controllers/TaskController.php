<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function store(Request $request, Project $project)
    {
        // Validamos los datos de la tarea
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Creamos la tarea vinculada a este proyecto
        $project->tasks()->create([
            'title' => $request->title,
            'description' => $request->description,
            'status' => 'pendiente', // Estado inicial por defecto
        ]);

        return redirect()->route('projects.show', $project)->with('success', 'Tarea añadida correctamente.');
    }

    // Actualizar nombre de la lista
    public function update(Request $request, Task $task)
    {
        $request->validate(['title' => 'required|string|max:255']);
        $task->update(['title' => $request->title]);
        
        return back()->with('success', 'Lista actualizada.');
    }

    // Eliminar lista completa
    public function destroy(Task $task)
    {
        $task->delete();
        return back()->with('success', 'Lista eliminada.');
    }

    // Duplicar lista con sus subtareas
    public function duplicate(Task $task)
    {
        $newTask = $task->replicate();
        $newTask->title = $task->title . ' (Copia)';
        $newTask->save();

        foreach ($task->subtasks as $subtask) {
            $newSubtask = $subtask->replicate();
            $newSubtask->task_id = $newTask->id;
            $newSubtask->save();
        }

        return back()->with('success', 'Lista duplicada correctamente.');
    }
}