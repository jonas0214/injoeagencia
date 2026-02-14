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
        $userId = Auth::id();
        // Cargamos proyectos y sus tareas con subtasks
        $projects = Project::where('user_id', $userId)->with('tasks.subtasks')->latest()->get();
        
        // Obtenemos el equipo real de la tabla team_members
        $team = \App\Models\TeamMember::all();

        // Retornamos la nueva vista estilo Asana
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
        // Cargamos las tareas de esta campaña publicitaria
        // Eager loading profundo para soportar: Sección -> Tarea -> Subtarea
        $project->load('tasks.subtasks.children');
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
} // <--- ESTA LLAVE DEBE SER LA ÚLTIMA