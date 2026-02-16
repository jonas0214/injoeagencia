<?php

namespace App\Http\Controllers;

use App\Models\Subtask;
use App\Models\Billing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BillingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $teamMember = $user->teamMember;
        $teamMemberId = $teamMember ? $teamMember->id : null;

        if ($user->role === 'colaborador') {
            // Historial de facturas enviadas
            $billings = Billing::where('team_member_id', $teamMemberId)->latest()->get();
            $facturedIds = $billings->pluck('task_ids')->flatten()->toArray();

            // Samuel Córdoba (ID 3)
            // Tareas asignadas:
            // 1. "levantar requerimientos" -> is_completed: true, is_approved: true
            // 2. "Diseño" -> is_completed: true, is_approved: true
            
            // Traemos TODAS las tareas no facturadas para que Samuel las vea,
            // pero en la vista bloquearemos las que no estén aprobadas.
            $availableTasks = Subtask::where('team_member_id', $teamMemberId)
                ->whereNotIn('id', $facturedIds)
                ->with(['task.project'])
                ->orderBy('is_approved', 'desc')
                ->orderBy('is_completed', 'desc')
                ->get();

            return view('billing.index', compact('billings', 'availableTasks'));
        }

        // Para administradores, ver todas las cuentas de cobro
        $billings = Billing::with('teamMember')->latest()->get();
        return view('billing.index', compact('billings'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $teamMember = $user->teamMember;

        if (!$teamMember) return back()->with('error', 'No tienes un perfil de colaborador vinculado.');

        $taskIds = $request->input('task_ids', []);
        if (empty($taskIds)) return back()->with('error', 'Debes seleccionar al menos una tarea.');

        $amount = $request->input('amount', 0);
        $tasks = Subtask::whereIn('id', $taskIds)->get();
        $totalTasksCount = $tasks->count();
        
        $lateTasksCount = 0;
        foreach ($tasks as $task) {
            if ($task->due_date && $task->due_date->isPast() && !$task->is_completed) {
                $lateTasksCount++;
            }
        }

        // El descuento es proporcional: (tareas vencidas / total tareas) * monto total
        $discount = $totalTasksCount > 0 ? ($amount * ($lateTasksCount / $totalTasksCount)) : 0;

        Billing::create([
            'team_member_id' => $teamMember->id,
            'reference' => 'CC-' . Carbon::now()->format('Ym') . '-' . strtoupper(substr(uniqid(), -4)),
            'subtotal' => $amount,
            'discount' => $discount,
            'amount' => max(0, $amount - $discount),
            'status' => 'pending',
            'notes' => $request->input('notes'),
            'task_ids' => $taskIds,
            'billed_at' => Carbon::now(),
        ]);

        return redirect()->route('billing.index')->with('success', 'Cuenta de cobro generada correctamente.');
    }

    public function updateStatus(Request $request, Billing $billing)
    {
        if (!in_array(Auth::user()->role, ['admin', 'ceo', 'contabilidad'])) {
            return back()->with('error', 'No tienes permisos.');
        }

        $billing->update(['status' => $request->status]);
        return back()->with('success', 'Estado actualizado.');
    }

    public function destroy(Billing $billing)
    {
        $user = Auth::user();
        
        // Solo puede eliminar si es administrador o si la cuenta es suya y no está pagada
        $isOwner = $user->teamMember && $billing->team_member_id === $user->teamMember->id;
        $canDelete = $user->role === 'admin' || ($isOwner && in_array($billing->status, ['pending', 'rejected']));

        if ($canDelete) {
            $billing->delete();
            return back()->with('success', 'Registro de cobro eliminado y actividades liberadas.');
        }

        return back()->with('error', 'No tienes permisos para eliminar este registro o la cuenta ya ha sido procesada.');
    }
}
