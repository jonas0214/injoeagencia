<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AttendanceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\SubtaskController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\TeamMemberController;
use App\Http\Controllers\AttachmentController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [ProjectController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    // 0. Sistema de Asistencia QR
    Route::get('/attendance/scanner', [AttendanceController::class, 'scanner'])->name('attendance.scanner');
    Route::post('/attendance/scan', [AttendanceController::class, 'scan'])->name('attendance.scan');
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');

    // 1. Perfil de Usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 2. Rutas de la Agencia (Proyectos)
    Route::resource('projects', ProjectController::class);
    Route::post('/projects/{project}/generate-meta-strategy', [ProjectController::class, 'generateMetaStrategy'])->name('projects.generate-meta-strategy');

    // 3. Rutas de Tareas
    Route::post('/projects/{project}/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::post('/tasks/{task}/duplicate', [TaskController::class, 'duplicate'])->name('tasks.duplicate');
    Route::post('/tasks/{task}/move', [TaskController::class, 'move'])->name('tasks.move');

    // 4. Rutas de Subtareas
    Route::post('/tasks/{task}/subtasks', [SubtaskController::class, 'store'])->name('subtasks.store');
    Route::put('/subtasks/{subtask}', [SubtaskController::class, 'update'])->name('subtasks.update');
    Route::delete('/subtasks/{subtask}', [SubtaskController::class, 'destroy'])->name('subtasks.destroy');
    Route::post('/subtasks/{subtask}/duplicate', [SubtaskController::class, 'duplicate'])->name('subtasks.duplicate');
    Route::post('/subtasks/{subtask}/subtasks', [SubtaskController::class, 'storeChild'])->name('subtasks.children.store');

    Route::post('/subtasks/{subtask}/comments', [CommentController::class, 'store'])->name('subtasks.comments.store');
    Route::get('/subtasks-detail/{subtask}', function(\App\Models\Subtask $subtask) {
        return $subtask->load(['children', 'teamMember', 'attachments', 'comments.user', 'task', 'parent', 'task.project']);
    });
    
    // Archivos Adjuntos
    Route::post('/subtasks/{subtask}/attachments', [AttachmentController::class, 'store'])->name('subtasks.attachments.store');
    Route::delete('/attachments/{attachment}', [AttachmentController::class, 'destroy'])->name('subtasks.attachments.destroy');

    // 5. Rutas de Equipo (Protegidas)
    Route::get('/team', [TeamMemberController::class, 'index'])->name('team.index');
    
    Route::middleware([\App\Http\Middleware\CheckRole::class.':admin,ceo,rrhh,contabilidad'])->group(function () {
        Route::post('/team', [TeamMemberController::class, 'store'])->name('team.store');
        Route::get('/team/{teamMember}', [TeamMemberController::class, 'show'])->name('team.show');
        Route::get('/team/{teamMember}/edit', [TeamMemberController::class, 'edit'])->name('team.edit');
        Route::put('/team/{teamMember}', [TeamMemberController::class, 'update'])->name('team.update');
        Route::delete('/team/{teamMember}', [TeamMemberController::class, 'destroy'])->name('team.destroy');
        
        Route::patch('/billing/{billing}/status', [\App\Http\Controllers\BillingController::class, 'updateStatus'])->name('billing.status');
    });

    // Rutas de Cuentas de Cobro
    Route::get('/billing', [\App\Http\Controllers\BillingController::class, 'index'])->name('billing.index');
    Route::post('/billing', [\App\Http\Controllers\BillingController::class, 'store'])->name('billing.store');
    Route::delete('/billing/{billing}', [\App\Http\Controllers\BillingController::class, 'destroy'])->name('billing.destroy');
});

require __DIR__.'/auth.php';
