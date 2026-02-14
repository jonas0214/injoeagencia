<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Subtask;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Subtask $subtask)
    {
        $request->validate([
            'content' => 'required|string'
        ]);

        $comment = Comment::create([
            'subtask_id' => $subtask->id,
            'user_id' => auth()->id() ?? 1, // Usa el usuario logueado o el ID 1 por defecto
            'content' => $request->content
        ]);

        // Devolvemos el comentario creado con los datos del usuario para mostrarlo al instante
        return response()->json($comment->load('user'));
    }
}