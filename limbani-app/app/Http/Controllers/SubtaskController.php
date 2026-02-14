<?php

namespace App\Http\Controllers;

use App\Models\Subtask;
use App\Models\Task;
use Illuminate\Http\Request;

class SubtaskController extends Controller
{
    // Guardar nueva subtarea
    public function store(Request $request, Task $task)
    {
        $request->validate(['title' => 'required|string|max:255']);

        $task->subtasks()->create([
            'title' => $request->title,
            'due_date' => now(),
            'is_completed' => false,
        ]);

        return back()->with('success', 'Acción agregada correctamente.');
    }

    // Modificar subtarea existente
    public function update(Request $request, Subtask $subtask)
    {
        $request->validate(['title' => 'sometimes|required|string|max:255']);
        
        $subtask->update($request->all());

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Guardado correctamente', 'subtask' => $subtask]);
        }

        return back()->with('success', 'Acción actualizada.');
    }

    // Eliminar subtarea
    public function destroy(Subtask $subtask)
    {
        $subtask->delete();
        return back()->with('success', 'Acción eliminada.');
    }

    // Duplicar subtarea
    public function duplicate(Subtask $subtask)
    {
        $newSubtask = $subtask->replicate();
        $newSubtask->title = $subtask->title . ' (Copia)';
        $newSubtask->save();

        return back()->with('success', 'Acción duplicada.');
    }

    // Guardar subtarea HIJA (dentro de otra subtarea)
    public function storeChild(Request $request, Subtask $subtask)
    {
        $request->validate(['title' => 'required|string|max:255']);

        $child = $subtask->children()->create([
            'title' => $request->title,
            'task_id' => $subtask->task_id, // Hereda la tarea principal
            'due_date' => now(),
            'is_completed' => false,
        ]);

        if ($request->wantsJson()) {
            return response()->json($child);
        }
        return back()->with('success', 'Subtarea agregada.');
    }
}