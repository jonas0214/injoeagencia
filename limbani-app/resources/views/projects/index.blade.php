@extends('layouts.asana')

@section('content')
<div class="py-12 px-4 md:px-8 max-w-7xl mx-auto">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-4">
        <div>
            <h1 class="text-xl md:text-2xl font-light text-gray-900 dark:text-white tracking-wide">Gestión de Usuarios</h1>
            <p class="text-[10px] text-gray-500 uppercase tracking-widest mt-1">Crea, visualiza y elimina usuarios del sistema</p>
        </div>
        <a href="{{ route('users.create') }}" class="w-full md:w-auto bg-orange-500 hover:bg-orange-600 text-black font-bold py-3 px-6 rounded-full shadow-lg transition flex items-center justify-center gap-2 text-xs uppercase tracking-widest">
            <i class="fas fa-plus"></i> Nuevo Usuario
        </a>
    </div>

    <div class="bg-white dark:bg-white/[0.03] border border-gray-200 dark:border-white/10 rounded-3xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-white/5">
                <thead class="bg-gray-50 dark:bg-black/20">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nombre</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Rol</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Vinculado a Equipo</th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Acciones</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-white/[0.02] divide-y divide-gray-100 dark:divide-white/5">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-gray-200 dark:bg-gray-800 rounded-full flex items-center justify-center">
                                        <span class="text-sm font-bold text-gray-600 dark:text-gray-300">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 dark:bg-blue-500/10 text-blue-800 dark:text-blue-400">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($user->teamMember)
                                    <span class="text-green-600 dark:text-green-500 font-semibold flex items-center gap-2">
                                        <i class="fas fa-check-circle"></i> Sí
                                    </span>
                                @else
                                    <span class="text-gray-400 dark:text-gray-500 flex items-center gap-2">
                                        <i class="fas fa-times-circle"></i> No
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                @if(Auth::id() !== $user->id)
                                <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres eliminar a este usuario?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-500 dark:hover:text-red-400">Eliminar</button>
                                </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-500 italic">No hay usuarios registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection