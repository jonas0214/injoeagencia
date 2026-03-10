@extends('layouts.asana')

@section('content')
<div class="py-12 px-4 md:px-8 max-w-3xl mx-auto">
    <div class="mb-12 border-b border-gray-200 dark:border-white/5 pb-8">
        <h1 class="text-3xl font-light text-gray-900 dark:text-white tracking-wide mb-1">
            Crear <span class="font-medium text-gray-900 dark:text-white">Nuevo Usuario</span>
        </h1>
        <p class="text-gray-500 text-xs font-medium tracking-[0.15em] uppercase">Define los detalles y permisos del nuevo miembro del sistema</p>
    </div>

    <div class="bg-white dark:bg-white/[0.03] backdrop-blur-md border border-gray-200 dark:border-white/10 rounded-3xl p-8 shadow-sm dark:shadow-2xl">
        <form action="{{ route('users.store') }}" method="POST" class="space-y-8">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nombre -->
                <div class="space-y-2 col-span-2">
                    <label for="name" class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Nombre Completo</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required 
                           class="w-full bg-gray-50 dark:bg-[#111] border border-gray-200 dark:border-white/10 rounded-xl p-4 text-sm text-gray-900 dark:text-white focus:ring-1 focus:ring-orange-500 outline-none @error('name') border-red-500 @enderror">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Email -->
                <div class="space-y-2 col-span-2">
                    <label for="email" class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Correo Electrónico</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                           class="w-full bg-gray-50 dark:bg-[#111] border border-gray-200 dark:border-white/10 rounded-xl p-4 text-sm text-gray-900 dark:text-white focus:ring-1 focus:ring-orange-500 outline-none @error('email') border-red-500 @enderror">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Contraseña -->
                <div class="space-y-2">
                    <label for="password" class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Contraseña</label>
                    <input type="password" name="password" id="password" required
                           class="w-full bg-gray-50 dark:bg-[#111] border border-gray-200 dark:border-white/10 rounded-xl p-4 text-sm text-gray-900 dark:text-white focus:ring-1 focus:ring-orange-500 outline-none @error('password') border-red-500 @enderror">
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Confirmar Contraseña -->
                <div class="space-y-2">
                    <label for="password_confirmation" class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Confirmar Contraseña</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                           class="w-full bg-gray-50 dark:bg-[#111] border border-gray-200 dark:border-white/10 rounded-xl p-4 text-sm text-gray-900 dark:text-white focus:ring-1 focus:ring-orange-500 outline-none">
                </div>

                <!-- Rol -->
                <div class="space-y-2">
                    <label for="role" class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Rol del Usuario</label>
                    <select name="role" id="role" required class="w-full bg-gray-50 dark:bg-[#111] border border-gray-200 dark:border-white/10 rounded-xl p-4 text-sm text-gray-900 dark:text-white focus:ring-1 focus:ring-orange-500 outline-none @error('role') border-red-500 @enderror">
                        <option value="admin">Admin</option>
                        <option value="ceo">CEO</option>
                        <option value="colaborador" selected>Colaborador</option>
                        <option value="rrhh">Recursos Humanos</option>
                        <option value="contabilidad">Contabilidad</option>
                    </select>
                    @error('role') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Cargo (para TeamMember) -->
                <div class="space-y-2">
                    <label for="position" class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Cargo (Opcional)</label>
                    <input type="text" name="position" id="position" value="{{ old('position') }}"
                           class="w-full bg-gray-50 dark:bg-[#111] border border-gray-200 dark:border-white/10 rounded-xl p-4 text-sm text-gray-900 dark:text-white focus:ring-1 focus:ring-orange-500 outline-none"
                           placeholder="Ej: Diseñador Gráfico">
                    <p class="text-[9px] text-gray-500 mt-1">Llenar si es un 'Colaborador' para crear su perfil de equipo.</p>
                </div>
            </div>

            <div class="flex gap-4 pt-8 border-t border-gray-200 dark:border-white/10">
                <a href="{{ route('users.index') }}" class="flex-1 py-4 rounded-2xl bg-gray-100 dark:bg-white/5 border border-gray-200 dark:border-white/10 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest text-center hover:bg-gray-200 dark:hover:bg-white/10 transition-all">Cancelar</a>
                <button type="submit" class="flex-[2] py-4 rounded-2xl bg-orange-600 text-white font-bold text-xs uppercase tracking-[0.2em] hover:bg-orange-700 transition-all shadow-xl shadow-orange-900/20">
                    Crear Usuario
                </button>
            </div>
        </form>
    </div>
</div>
@endsection