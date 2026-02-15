<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Subtask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    public function store(Request $request, Subtask $subtask)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
        ]);

        $file = $request->file('file');
        $path = $file->store('attachments', 'public');

        $attachment = $subtask->attachments()->create([
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $this->getFileType($file->getMimeType()),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
        ]);

        return response()->json($attachment);
    }

    public function destroy(Attachment $attachment)
    {
        Storage::disk('public')->delete($attachment->file_path);
        $attachment->delete();
        return response()->json(['success' => true]);
    }

    private function getFileType($mime)
    {
        if (str_starts_with($mime, 'image/')) return 'image';
        if (str_contains($mime, 'pdf')) return 'pdf';
        if (str_contains($mime, 'word') || str_contains($mime, 'officedocument.wordprocessingml')) return 'word';
        if (str_contains($mime, 'excel') || str_contains($mime, 'officedocument.spreadsheetml')) return 'excel';
        return 'file';
    }
}
