@extends('layouts.asana')

@section('content')
<style>
    body {
        background: #0f1012 !important;
        min-height: 100vh;
        color: white;
    }
    .profile-card {
        background: rgba(255, 255, 255, 0.03);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.08);
    }
    .input-field {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: white;
        transition: all 0.3s ease;
    }
    .input-field:focus {
        background: rgba(255, 255, 255, 0.08);
        border-color: #ffaa00;
        ring: 0;
    }
</style>

<div class="py-12 px-8 max-w-[900px] mx-auto">
    
    <!-- Header Navegación -->
    <div class="flex items-center gap-4 mb-12">
        <a href="{{ route('team.show', $teamMember) }}" class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-gray-400 hover:text-white transition-colors">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h2 class="text-sm font-bold text-gray-500 uppercase tracking-[0.2em]">Editar Ficha de Colaborador</h2>
    </div>

    <form action="{{ route('team.update', $teamMember) }}" method="POST" class="profile-card rounded-3xl p-10 space-y-8" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="flex flex-col md:flex-row items-center gap-8 mb-4">
            <div class="w-24 h-24 rounded-3xl border border-white/10 overflow-hidden bg-white/5 shrink-0">
                @if($teamMember->photo)
                    <img src="{{ asset('storage/' . $teamMember->photo) }}" alt="" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-gray-600">
                        <i class="fas fa-user text-3xl"></i>
                    </div>
                @endif
            </div>
            <div class="space-y-2 flex-1">
                <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Cambiar Foto de Perfil</label>
                <input type="file" name="photo" accept="image/*"
                       class="w-full text-xs text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-white/5 file:text-gray-300 hover:file:bg-white/10">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Nombre -->
            <div class="space-y-2">
                <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Nombre Completo</label>
                <input type="text" name="name" value="{{ old('name', $teamMember->name) }}" required 
                       class="w-full input-field rounded-xl p-4 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
            </div>

            <!-- Cargo -->
            <div class="space-y-2">
                <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Cargo en la Agencia</label>
                <input type="text" name="position" value="{{ old('position', $teamMember->position) }}" required 
                       class="w-full input-field rounded-xl p-4 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
            </div>

            <!-- Cédula -->
            <div class="space-y-2">
                <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Cédula / Identificación</label>
                <input type="text" name="cedula" value="{{ old('cedula', $teamMember->cedula) }}" required 
                       class="w-full input-field rounded-xl p-4 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
            </div>

            <!-- Fecha Nacimiento -->
            <div class="space-y-2">
                <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Fecha de Nacimiento</label>
                <input type="date" name="birth_date" value="{{ old('birth_date', $teamMember->birth_date ? $teamMember->birth_date->format('Y-m-d') : '') }}" 
                       class="w-full input-field rounded-xl p-4 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
            </div>

            <!-- Email -->
            <div class="space-y-2">
                <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Correo Electrónico</label>
                <input type="email" name="email" value="{{ old('email', $teamMember->email) }}" 
                       class="w-full input-field rounded-xl p-4 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
            </div>

            <!-- Teléfono -->
            <div class="space-y-2">
                <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Teléfono Móvil</label>
                <input type="text" name="phone" value="{{ old('phone', $teamMember->phone) }}" 
                       class="w-full input-field rounded-xl p-4 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
            </div>

            <!-- Dirección -->
            <div class="col-span-1 md:col-span-2 space-y-2">
                <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Dirección de Residencia</label>
                <input type="text" name="address" value="{{ old('address', $teamMember->address) }}" 
                       class="w-full input-field rounded-xl p-4 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
            </div>

            <!-- Salario -->
            <div class="space-y-2">
                <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Asignación Salarial ($)</label>
                <input type="number" step="0.01" name="salary" value="{{ old('salary', $teamMember->salary) }}" required
                       class="w-full input-field rounded-xl p-4 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
            </div>

            <!-- Rol -->
            <div class="space-y-2">
                <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Rol del Sistema</label>
                <select name="role" required class="w-full input-field rounded-xl p-4 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                    <option value="colaborador" {{ (old('role', $teamMember->user->role ?? '') == 'colaborador') ? 'selected' : '' }}>Colaborador</option>
                    <option value="ceo" {{ (old('role', $teamMember->user->role ?? '') == 'ceo') ? 'selected' : '' }}>CEO</option>
                    <option value="rrhh" {{ (old('role', $teamMember->user->role ?? '') == 'rrhh') ? 'selected' : '' }}>Recursos Humanos</option>
                    <option value="contabilidad" {{ (old('role', $teamMember->user->role ?? '') == 'contabilidad') ? 'selected' : '' }}>Contabilidad</option>
                    <option value="admin" {{ (old('role', $teamMember->user->role ?? '') == 'admin') ? 'selected' : '' }}>Administrador (Soporte)</option>
                </select>
            </div>

            <!-- Detalles Bancarios -->
            <div class="col-span-1 md:col-span-2 space-y-2">
                <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Información de Tesorería (Banco, Cuenta, Nequi)</label>
                <textarea name="bank_details" rows="3" 
                          class="w-full input-field rounded-xl p-4 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500 resize-none">{{ old('bank_details', $teamMember->bank_details) }}</textarea>
            </div>
        </div>

        <div class="pt-8 border-t border-white/5 flex justify-end gap-4">
            <a href="{{ route('team.show', $teamMember) }}" class="py-4 px-8 rounded-xl bg-white/5 border border-white/10 text-xs font-bold text-gray-400 uppercase tracking-widest hover:bg-white/10 transition-colors">
                Cancelar
            </a>
            <button type="submit" class="py-4 px-8 rounded-xl bg-white text-black font-bold text-xs uppercase tracking-widest hover:bg-gray-200 transition-colors">
                Guardar Cambios
            </button>
        </div>
    </form>

</div>
@endsection
