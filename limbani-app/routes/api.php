<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SubtaskApiController;


Route::post('/subtasks/{id}/ai-suggestion', [SubtaskApiController::class, 'updateAiSuggestion']);

Route::get('/subtasks/{id}', [SubtaskApiController::class, 'show']);