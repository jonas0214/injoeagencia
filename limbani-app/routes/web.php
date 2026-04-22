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
use App\Http\Controllers\UserController;


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
    Route::post('/projects/reorder', [ProjectController::class, 'reorder'])->name('projects.reorder');
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
    Route::post('/subtasks/reorder', [SubtaskController::class, 'reorder'])->name('subtasks.reorder');

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

    // Rutas de Briefs (Formularios dinámicos para clientes)
    Route::get('/projects/{project}/brief', [\App\Http\Controllers\BriefController::class, 'edit'])->name('briefs.edit');
    Route::put('/projects/{project}/brief', [\App\Http\Controllers\BriefController::class, 'update'])->name('briefs.update');
    Route::get('/projects/{project}/brief/show', [\App\Http\Controllers\BriefController::class, 'show'])->name('briefs.show');
    Route::get('/projects/{project}/brief/download', [\App\Http\Controllers\BriefController::class, 'download'])->name('briefs.download');
    Route::get('/projects/{project}/brief/status', [\App\Http\Controllers\BriefController::class, 'status'])->name('briefs.status');
    Route::post('/projects/{project}/brief/ai-suggestions', [\App\Http\Controllers\BriefAIController::class, 'getSuggestions'])->name('briefs.ai-suggestions');

    // Rutas de Cuentas de Cobro
    Route::get('/billing', [\App\Http\Controllers\BillingController::class, 'index'])->name('billing.index');
    Route::post('/billing', [\App\Http\Controllers\BillingController::class, 'store'])->name('billing.store');
    Route::delete('/billing/{billing}', [\App\Http\Controllers\BillingController::class, 'destroy'])->name('billing.destroy');

    // 7. Gestión Administrativa & Contratos
    Route::get('/admin-projects', [\App\Http\Controllers\AdministrativeProjectController::class, 'index'])->name('admin-projects.index');
    Route::post('/admin-projects', [\App\Http\Controllers\AdministrativeProjectController::class, 'store'])->name('admin-projects.store');
    Route::post('/contracts', [\App\Http\Controllers\ContractController::class, 'store'])->name('contracts.store');
    Route::get('/contracts/{contract}/print', [\App\Http\Controllers\ContractController::class, 'print'])->name('contracts.print');

    // 8. Gestion de Usuarios (Admin/CEO)
    Route::resource('users', UserController::class)->except(['show', 'edit', 'update']);



    // Notificaciones
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
});

require __DIR__.'/auth.php';
