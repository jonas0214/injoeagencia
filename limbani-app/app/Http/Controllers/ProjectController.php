<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    // 1. Vista principal del Dashboard
    public function index()
    {
        $user = Auth::user();
        
        if ($user->role === 'colaborador') {
            $teamMember = $user->teamMember;
            $teamMemberId = $teamMember ? $teamMember->id : null;

            // Un colaborador solo ve los proyectos donde tiene subtareas asignadas
            $projects = Project::whereHas('tasks.subtasks', function($query) use ($teamMemberId) {
                $query->where('team_member_id', $teamMemberId);
            })->with(['tasks.subtasks' => function($query) use ($teamMemberId) {
                $query->where('team_member_id', $teamMemberId)
                      ->with(['children', 'teamMember', 'attachments', 'comments.user', 'task', 'parent', 'task.project']);
            }])->latest()->get();
            
            // Solo ve a sus compañeros de equipo de los proyectos en los que participa
            $team = \App\Models\TeamMember::whereIn('id', function($query) use ($projects) {
                $query->select('team_member_id')
                    ->from('subtasks')
                    ->whereIn('task_id', function($q) use ($projects) {
                        $q->select('id')->from('tasks')->whereIn('project_id', $projects->pluck('id'));
                    });
            })->get();
        } else {
            // Administradores y otros roles ven todo
            $projects = Project::with(['tasks.subtasks' => function($q) {
                $q->with(['children', 'teamMember', 'attachments', 'comments.user', 'task', 'parent', 'task.project']);
            }])->latest()->get();
            $team = \App\Models\TeamMember::all();
        }

        return view('dashboard_asana', compact('projects', 'team'));
    }

    // 2. Mostrar el formulario de nueva campaña
    public function create()
    {
        return view('projects.create');
    }

    // 3. Guardar la campaña en la base de datos
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Project::create([
            'name' => $request->name,
            'description' => $request->description,
            'user_id' => Auth::id(),
            'status' => 'activo',
        ]);

        return redirect()->route('dashboard')->with('success', '¡Campaña creada con éxito!');
    }

    public function show(Project $project)
    {
        $user = Auth::user();
        
        if ($user->role === 'colaborador') {
            $teamMemberId = $user->teamMember ? $user->teamMember->id : null;
            
            // Cargar solo las secciones que tienen tareas asignadas al colaborador, 
            // y dentro de esas secciones solo sus tareas.
            $project->load(['tasks' => function($query) use ($teamMemberId) {
                $query->whereHas('subtasks', function($q) use ($teamMemberId) {
                    $q->where('team_member_id', $teamMemberId);
                })->with(['subtasks' => function($q) use ($teamMemberId) {
                    $q->where('team_member_id', $teamMemberId)
                      ->with(['children', 'teamMember', 'attachments', 'comments.user', 'task', 'parent', 'task.project']);
                }]);
            }]);
        } else {
            $project->load(['tasks.subtasks' => function($q) {
                $q->with(['children', 'teamMember', 'attachments', 'comments.user', 'task', 'parent', 'task.project']);
            }]);
        }

        return view('projects.show', compact('project'));
    }

    // 4. Mostrar formulario de edición (reutilizando create)
    public function edit(Project $project)
    {
        return view('projects.create', compact('project'));
    }

    // 5. Actualizar el proyecto en BD
    public function update(Request $request, Project $project)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $project->update($request->all());

        return redirect()->route('dashboard')->with('success', 'Proyecto actualizado correctamente.');
    }

    // 6. Eliminar el proyecto
    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('dashboard')->with('success', 'Proyecto eliminado correctamente.');
    }
}
