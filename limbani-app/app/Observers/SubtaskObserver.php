<?php

namespace App\Observers;

use App\Models\Subtask;
use App\Jobs\SendSubtaskToN8n;
use Illuminate\Support\Facades\Log;

class SubtaskObserver
{
    /**
     * Se ejecuta automáticamente cuando se CREA una subtarea.
     */
    public function created(Subtask $subtask): void
    {
        // Despachar el Job a la cola para que sea asíncrono
        SendSubtaskToN8n::dispatch($subtask);
    }
}