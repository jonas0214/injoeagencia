<?php

namespace App\Http\Controllers;

use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class TeamMemberController extends Controller
{
    public function index()
    {
        $team = TeamMember::with('user')->orderBy('name')->get();
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
            'email' => 'required|email|unique:users,email',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('team-photos', 'public');
            $data['photo'] = $path;
        }

        // Crear el usuario primero
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['cedula']), // Password por defecto es su cédula
            'role' => $request->input('role', 'colaborador'),
        ]);

        $data['user_id'] = $user->id;
        $teamMember = TeamMember::create($data);

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
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
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

        // Actualizar o crear usuario
        $role = $request->input('role', 'colaborador');
        
        if ($teamMember->user_id) {
            // Actualizar usuario existente
            $teamMember->user->update([
                'name' => $data['name'],
                'email' => $data['email'],
                'role' => $role
            ]);
        } else if ($teamMember->email) {
            // Intentar vincular por email o crear uno nuevo
            $user = User::where('email', $teamMember->email)->first();
            
            if (!$user) {
                $user = User::create([
                    'name' => $teamMember->name,
                    'email' => $teamMember->email,
                    'password' => Hash::make($teamMember->cedula),
                    'role' => $role,
                ]);
            } else {
                $user->update(['role' => $role]);
            }
            
            $teamMember->update(['user_id' => $user->id]);
        }

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