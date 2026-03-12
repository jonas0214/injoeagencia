<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Brief;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BriefController extends Controller
{
    /**
     * Show the form for creating/editing a brief.
     */
    public function edit(Project $project)
    {
        $brief = $project->getOrCreateBrief();
        
        return view('briefs.edit', [
            'project' => $project,
            'brief' => $brief,
        ]);
    }

    /**
     * Update the specified brief in storage.
     */
    public function update(Request $request, Project $project)
    {
        $brief = $project->getOrCreateBrief();
        
        $validated = $request->validate([
            'answers' => 'nullable|array',
            'objectives' => 'nullable|string|max:2000',
            'target_audience' => 'nullable|string|max:1000',
            'key_dates' => 'nullable|string|max:1000',
            'budget' => 'nullable|numeric|min:0|max:999999999.99',
            'special_requirements' => 'nullable|string|max:2000',
            'key_messages' => 'nullable|string|max:1000',
            'success_metrics' => 'nullable|string|max:1000',
            'competitor_analysis' => 'nullable|string|max:2000',
            'brand_guidelines' => 'nullable|string|max:2000',
            'content_preferences' => 'nullable|string|max:1000',
            'status' => ['nullable', Rule::in(['draft', 'submitted', 'reviewed', 'approved'])],
        ]);

        // Update the brief
        $brief->update($validated);

        // Handle status changes
        if ($request->has('action')) {
            switch ($request->action) {
                case 'submit':
                    $brief->markAsSubmitted();
                    break;
                case 'review':
                    $brief->markAsReviewed();
                    break;
                case 'approve':
                    $brief->markAsApproved();
                    break;
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Brief actualizado correctamente',
                'brief' => $brief,
            ]);
        }

        return redirect()->route('projects.show', $project)
            ->with('success', 'Brief actualizado correctamente');
    }

    /**
     * Show brief in read-only mode.
     */
    public function show(Project $project)
    {
        $brief = $project->brief;
        
        if (!$brief) {
            abort(404, 'No se encontró un brief para este proyecto');
        }

        return view('briefs.show', [
            'project' => $project,
            'brief' => $brief,
        ]);
    }

    /**
     * Download brief as PDF.
     */
    public function download(Project $project)
    {
        $brief = $project->brief;
        
        if (!$brief) {
            abort(404, 'No se encontró un brief para este proyecto');
        }

        // TODO: Implement PDF generation
        return response()->json([
            'message' => 'PDF generation not implemented yet',
            'brief' => $brief,
        ]);
    }

    /**
     * Get brief status for AJAX requests.
     */
    public function status(Project $project)
    {
        $brief = $project->brief;
        
        return response()->json([
            'has_brief' => $brief ? true : false,
            'status' => $brief ? $brief->status : 'none',
            'status_label' => $brief ? $this->getStatusLabel($brief->status) : 'Sin brief',
            'last_updated' => $brief ? $brief->updated_at->diffForHumans() : null,
        ]);
    }

    /**
     * Get human-readable status label.
     */
    private function getStatusLabel($status): string
    {
        $labels = [
            'draft' => 'Borrador',
            'submitted' => 'Enviado',
            'reviewed' => 'Revisado',
            'approved' => 'Aprobado',
        ];

        return $labels[$status] ?? $status;
    }
}