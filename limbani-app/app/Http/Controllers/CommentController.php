<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Subtask;
use Illuminate\Http\Request;
use App\Notifications\NewCommentNotification;
use App\Models\User;

class CommentController extends Controller
{
    public function store(Request $request, Subtask $subtask)
    {
        $request->validate([
            'content' => 'nullable|string',
            'image' => 'nullable|string' // Base64 image string
        ]);

        $imagePath = null;
        if ($request->image) {
            $imageData = $request->image;
            if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
                $imageData = substr($imageData, strpos($imageData, ',') + 1);
                $type = strtolower($type[1]); // jpg, png, gif

                if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                    throw new \Exception('invalid image type');
                }
                $imageData = base64_decode($imageData);

                if ($imageData === false) {
                    throw new \Exception('base64_decode failed');
                }

                $fileName = uniqid() . '.' . $type;
                $imagePath = 'comments/' . $fileName;
                \Storage::disk('public')->put($imagePath, $imageData);
            }
        }

        $comment = Comment::create([
            'subtask_id' => $subtask->id,
            'user_id' => auth()->id() ?? 1,
            'content' => $request->content ?? '',
            'image_path' => $imagePath
        ]);

        // Notificar al responsable de la tarea
        if ($subtask->teamMember && $subtask->teamMember->user_id && $subtask->teamMember->user_id !== auth()->id()) {
            $subtask->teamMember->user->notify(new NewCommentNotification($comment, $subtask));
        }

        // Notificar a Administrativos (opcional, pero útil para CEO)
        $admins = User::whereIn('role', ['admin', 'ceo'])->where('id', '!=', auth()->id())->get();
        foreach($admins as $admin) {
             // Solo notificar si no es el responsable ya notificado
             if (!$subtask->teamMember || $admin->id !== $subtask->teamMember->user_id) {
                 $admin->notify(new NewCommentNotification($comment, $subtask));
             }
        }

        return response()->json($comment->load('user'));
    }
}