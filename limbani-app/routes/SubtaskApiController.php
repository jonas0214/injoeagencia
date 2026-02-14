<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subtask;
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
}