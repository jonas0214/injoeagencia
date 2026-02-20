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
            'due_date' => null,
            'is_completed' => false,
            'position' => $task->subtasks()->count(),
        ]);

        return back()->with('success', 'Acción agregada correctamente.');
    }

    // Modificar subtarea existente
    public function update(Request $request, Subtask $subtask)
    {
        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'team_member_id' => 'nullable',
            'due_date' => 'nullable',
            'description' => 'nullable|string',
            'is_completed' => 'nullable',
            'is_approved' => 'nullable'
        ]);

        $data = $request->only(['title', 'description', 'due_date', 'team_member_id']);

        if ($request->has('is_completed')) {
            $data['is_completed'] = filter_var($request->is_completed, FILTER_VALIDATE_BOOLEAN);
        }

        // Solo administradores pueden aprobar
        if ($request->has('is_approved') && in_array(auth()->user()->role, ['admin', 'ceo'])) {
            $data['is_approved'] = filter_var($request->is_approved, FILTER_VALIDATE_BOOLEAN);
            if ($data['is_approved'] && !$subtask->is_approved) {
                $data['approved_at'] = now();
                $data['approved_by'] = auth()->id();
            }
        }

        if ($request->has('team_member_id')) {
            $data['team_member_id'] = empty($request->team_member_id) ? null : $request->team_member_id;
        }

        $subtask->update($data);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Guardado correctamente',
                'subtask' => $subtask->load('teamMember')
            ]);
        }

        return back()->with('success', 'Acción actualizada.');
    }

    // Eliminar subtarea
    public function destroy(Request $request, Subtask $subtask)
    {
        \Log::info("Eliminando Subtask ID: " . $subtask->id);
        $subtask->delete();

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

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
            'due_date' => null,
            'is_completed' => false,
            'position' => $subtask->children()->count(),
        ]);

        if ($request->wantsJson()) {
            return response()->json($child);
        }
        return back()->with('success', 'Subtarea agregada.');
    }
}