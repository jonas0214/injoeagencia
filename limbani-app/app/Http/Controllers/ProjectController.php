<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
            })->with(['tasks' => function($query) use ($teamMemberId) {
                $query->whereHas('subtasks', function($q) use ($teamMemberId) {
                    $q->where('team_member_id', $teamMemberId);
                })->with(['subtasks' => function($q) use ($teamMemberId) {
                    $q->where('team_member_id', $teamMemberId)
                      ->with(['children', 'teamMember', 'attachments', 'comments.user', 'task.project', 'parent']);
                }]);
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
            $projects = Project::with(['tasks' => function($q) {
                $q->with(['subtasks' => function($sq) {
                    $sq->with(['children', 'teamMember', 'attachments', 'comments.user', 'task.project', 'parent']);
                }]);
            }])->latest()->get();
            $team = \App\Models\TeamMember::all();
        }

        return view('dashboard_asana', compact('projects', 'team'));
    }

    // 2. Mostrar el formulario de nueva campaña
    public function create()
    {
        $templates = Project::where('is_template', true)->get();
        return view('projects.create', compact('templates'));
    }

    // 3. Guardar la campaña en la base de datos
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'template_id' => 'nullable|exists:projects,id',
        ]);

        DB::transaction(function () use ($request) {
            $project = Project::create([
                'name' => $request->name,
                'description' => $request->description,
                'user_id' => Auth::id(),
                'status' => 'activo',
                'is_template' => $request->has('is_template'),
            ]);

            // Crear automáticamente la sección de PROGRAMACIÓN META ADS
            $metaSection = $project->tasks()->create([
                'title' => 'PROGRAMACIÓN META ADS',
                'position' => 0
            ]);

            // Añadir tareas base de Meta Ads
            $metaSection->subtasks()->createMany([
                ['title' => 'Definición de Público Objetivo', 'position' => 0],
                ['title' => 'Diseño de Creativos (Artes)', 'position' => 1],
                ['title' => 'Redacción de Copywriting', 'position' => 2],
                ['title' => 'Montaje en Business Manager', 'position' => 3],
            ]);

            // Si se seleccionó una plantilla, clonar sus tareas y subtareas
            if ($request->filled('template_id')) {
                $template = Project::with('tasks.subtasks')->find($request->template_id);
                
                foreach ($template->tasks as $templateSection) {
                    $newSection = $project->tasks()->create([
                        'title' => $templateSection->title,
                        'position' => $templateSection->position,
                    ]);

                    foreach ($templateSection->subtasks->whereNull('parent_id') as $templateSubtask) {
                        $this->cloneSubtask($templateSubtask, $newSection->id, null);
                    }
                }
            }
        });

        return redirect()->route('dashboard')->with('success', '¡Proyecto creado con éxito!');
    }

    /**
     * Clonar subtarea de forma recursiva
     */
    private function cloneSubtask($templateSubtask, $taskId, $parentId = null)
    {
        $newSubtask = \App\Models\Subtask::create([
            'title' => $templateSubtask->title,
            'description' => $templateSubtask->description,
            'task_id' => $taskId,
            'parent_id' => $parentId,
            'position' => $templateSubtask->position,
            'is_completed' => false,
        ]);

        // Clonar hijos recursivamente
        foreach ($templateSubtask->children as $child) {
            $this->cloneSubtask($child, $taskId, $newSubtask->id);
        }
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
            $project->load(['tasks' => function($q) {
                $q->with(['subtasks' => function($sq) {
                    $sq->with(['children', 'teamMember', 'attachments', 'comments.user', 'task.project', 'parent']);
                }]);
            }]);
        }

        return view('projects.show', compact('project'));
    }

    // 4. Mostrar formulario de edición (reutilizando create)
    public function edit(Project $project)
    {
        $templates = Project::where('is_template', true)->where('id', '!=', $project->id)->get();
        return view('projects.create', compact('project', 'templates'));
    }

    // 5. Actualizar el proyecto en BD
    public function update(Request $request, Project $project)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $data = $request->all();
        $data['is_template'] = $request->has('is_template');

        $project->update($data);

        return redirect()->route('dashboard')->with('success', 'Proyecto actualizado correctamente.');
    }

    // 6. Eliminar el proyecto
    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('dashboard')->with('success', 'Proyecto eliminado correctamente.');
    }

    public function generateMetaStrategy(Project $project)
    {
        $metaSection = $project->tasks()->firstOrCreate(['title' => 'PROGRAMACIÓN META ADS'], ['position' => 0]);

        // Simular o llamar a n8n para obtener una estrategia ganadora
        $strategies = [
            ['title' => 'REEL: El secreto detrás de ' . $project->name, 'desc' => 'Estrategia de curiosidad. Gancho: 3 segundos. Valor: Beneficio oculto.'],
            ['title' => 'ESTÁTICO: Testimonios de clientes satisfechos', 'desc' => 'Prueba social. Usar carrusel de capturas de pantalla de WhatsApp.'],
            ['title' => 'VIDEO: ¿Por qué elegirnos sobre la competencia?', 'desc' => 'Diferenciación competitiva. Mostrar procesos internos.'],
            ['title' => 'AD: Oferta Irresistible - Solo por tiempo limitado', 'desc' => 'Conversión directa. Escasez y urgencia.'],
        ];

        foreach ($strategies as $index => $strat) {
            $metaSection->subtasks()->create([
                'title' => $strat['title'],
                'description' => $strat['desc'],
                'due_date' => now()->addDays($index * 2 + 1)->setHour(9)->setMinute(0),
                'position' => $index + 10,
                'ai_suggestion' => 'Sugerencia de IA: Este contenido tiene un potencial de alcance proyectado de +40% para el sector de ' . $project->name . '.'
            ]);
        }

        // Señal a n8n
        try {
            \Illuminate\Support\Facades\Http::post('https://n8n.srv1317921.hstgr.cloud/webhook-test/generar-estrategia-meta', [
                'project_id' => $project->id,
                'project_name' => $project->name,
            ]);
        } catch (\Exception $e) {}

        return response()->json(['success' => true]);
    }
}
