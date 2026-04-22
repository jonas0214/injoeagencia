<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\TeamMember;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'team_member_id' => 'required|exists:team_members,id',
            'type' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'salary' => 'required|numeric',
            'position' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $contract = Contract::create($data);

        return redirect()->back()->with('success', 'Contrato generado con éxito.');
    }

    public function print(Contract $contract)
    {
        $contract->load('teamMember');
        return view('contracts.print', compact('contract'));
    }
}
