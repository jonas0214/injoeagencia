<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Muestra la lista de usuarios.
     * Accesible para Admin y CEO.
     */
    public function index()
    {
        // Verificación de roles permitidos (Admin y CEO)
        if (!in_array(Auth::user()->role, [User::ROLE_ADMIN, User::ROLE_CEO])) {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para acceder a la gestión de usuarios.');
        }

        $users = User::with('teamMember')->latest()->get();
        return view('users.index', compact('users'));
    }

    /**
     * Muestra el formulario de creación de usuario.
     */
    public function create()
    {
        if (!in_array(Auth::user()->role, [User::ROLE_ADMIN, User::ROLE_CEO])) {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos.');
        }
        return view('users.create');
    }

    /**
     * Almacena un nuevo usuario en la base de datos.
     */
    public function store(Request $request)
    {
        if (!in_array(Auth::user()->role, [User::ROLE_ADMIN, User::ROLE_CEO])) {
            return back()->with('error', 'No tienes permisos.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in([
                User::ROLE_ADMIN, 
                User::ROLE_CEO, 
                User::ROLE_COLABORADOR, 
                User::ROLE_RRHH, 
                User::ROLE_CONTABILIDAD
            ])],
            'position' => 'nullable|string|max:255', // Cargo opcional para TeamMember
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        // Crear perfil de TeamMember automáticamente si es colaborador o si se especifica cargo.
        // Esto asegura que el usuario aparezca en los selectores de tareas y pueda facturar.
        if ($request->role === User::ROLE_COLABORADOR || $request->filled('position')) {
            TeamMember::create([
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'position' => $request->input('position', 'Colaborador'),
            ]);
        }

        return redirect()->route('users.index')->with('success', 'Usuario creado exitosamente.');
    }

    /**
     * Eliminar usuario.
     */
    public function destroy(User $user)
    {
        if (!in_array(Auth::user()->role, [User::ROLE_ADMIN, User::ROLE_CEO])) {
            return back()->with('error', 'No tienes permisos.');
        }

        if ($user->id === Auth::id()) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $user->delete();
        return back()->with('success', 'Usuario eliminado.');
    }
}