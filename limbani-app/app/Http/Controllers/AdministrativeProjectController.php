<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdministrativeProjectController extends Controller
{
    /**
     * Display a listing of administrative projects.
     */
    public function index(Request $request)
    {
        $category = $request->get('category', Project::CAT_RRHH); // Default to RRHH
        
        $projects = Project::where('category', $category)
            ->with(['tasks' => function($q) {
                $q->with(['subtasks' => function($sq) {
                    $sq->with(['children', 'teamMember', 'attachments', 'comments.user', 'task.project', 'parent']);
                }]);
            }])
            ->orderBy('position')
            ->latest()
            ->get();

        $team = TeamMember::all();

        return view('projects.admin_index', compact('projects', 'team', 'category'));
    }

    /**
     * Store a newly created administrative project.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|in:rrhh,administrativo,contabilidad',
            'description' => 'nullable|string',
        ]);

        $project = Project::create([
            'name' => $request->name,
            'description' => $request->description,
            'category' => $request->category,
            'user_id' => Auth::id(),
            'status' => 'activo',
        ]);

        return redirect()->route('admin-projects.index', ['category' => $request->category])
            ->with('success', 'Proyecto administrativo creado con éxito.');
    }
}
