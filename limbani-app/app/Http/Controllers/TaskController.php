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