<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subtask;
use App\Models\Comment;
use Illuminate\Http\Request;

class SubtaskApiController extends Controller
{
    /**
     * Recibe la sugerencia de IA desde n8n y actualiza la subtarea.
     */
    public function updateAiSuggestion(Request $request, $id)
    {
        // Validamos que venga el texto
        $request->validate([
            'ai_suggestion' => 'required|string'
        ]);

        $subtask = Subtask::findOrFail($id);

        $subtask->update([
            'ai_suggestion' => $request->ai_suggestion
        ]);

        return response()->json(['message' => 'Sugerencia actualizada con éxito']);
    }

    /**
     * Obtiene los datos frescos de una subtarea (incluyendo comentarios y sugerencia IA actualizada).
     */
    public function show($id)
    {
        $subtask = Subtask::findOrFail($id);
        // Cargamos los comentarios manualmente
        $subtask->comments = Comment::where('subtask_id', $id)->with('user')->latest()->get();
        // Cargamos las subtareas hijas (para mostrarlas en el panel)
        $subtask->load('children');
        
        return response()->json($subtask);
    }
}