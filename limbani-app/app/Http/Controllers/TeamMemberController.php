<?php

namespace App\Http\Controllers;

use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TeamMemberController extends Controller
{
    public function index()
    {
        $team = TeamMember::orderBy('name')->get();
        return view('team.index', compact('team'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'cedula' => 'required|string|unique:team_members,cedula',
            'position' => 'required|string',
            'salary' => 'required|numeric',
            'phone' => 'nullable|string',
            'bank_details' => 'nullable|string',
            'email' => 'nullable|email',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('team-photos', 'public');
            $data['photo'] = $path;
        }

        TeamMember::create($data);

        return redirect()->back()->with('success', 'Colaborador agregado correctamente.');
    }

    public function show(TeamMember $teamMember)
    {
        return view('team.show', compact('teamMember'));
    }

    public function edit(TeamMember $teamMember)
    {
        return view('team.edit', compact('teamMember'));
    }

    public function update(Request $request, TeamMember $teamMember)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'cedula' => 'required|string|unique:team_members,cedula,'.$teamMember->id,
            'position' => 'required|string',
            'salary' => 'required|numeric',
            'phone' => 'nullable|string',
            'bank_details' => 'nullable|string',
            'email' => 'nullable|email',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('photo')) {
            // Eliminar foto anterior si existe
            if ($teamMember->photo) {
                Storage::disk('public')->delete($teamMember->photo);
            }
            $path = $request->file('photo')->store('team-photos', 'public');
            $data['photo'] = $path;
        }

        $teamMember->update($data);

        return redirect()->route('team.show', $teamMember)->with('success', 'Información actualizada correctamente.');
    }

    public function destroy(TeamMember $teamMember)
    {
        if ($teamMember->photo) {
            Storage::disk('public')->delete($teamMember->photo);
        }
        $teamMember->delete();
        return redirect()->route('team.index')->with('success', 'Colaborador eliminado de la agencia.');
    }
}