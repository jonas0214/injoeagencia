@extends('layouts.asana')

@section('content')
<style>
    /* Estilos forzados para garantizar visibilidad en modo claro y oscuro */
    .profile-card {
        background: white !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
    }
    
    .input-field {
        background: #f8fafc !important;
        border: 2px solid #e2e8f0 !important;
        color: #1a202c !important;
        font-weight: 500 !important;
        transition: all 0.3s ease;
    }
    
    .input-field:focus {
        background: white !important;
        border-color: #ffaa00 !important;
        box-shadow: 0 0 0 3px rgba(255, 170, 0, 0.2) !important;
        outline: none !important;
    }

    label {
        color: #4a5568 !important;
        margin-bottom: 0.5rem !important;
        display: block !important;
    }

    /* Ajuste para que el texto de las opciones del select sea visible */
    select.input-field option {
        color: #1a202c !important;
        background: white !important;
    }

    /* Sobrescribir estilos de modo oscuro de Tailwind que puedan interferir */
    .dark .input-field {
        background: #f8fafc !important;
        color: #1a202c !important;
        border-color: #e2e8f0 !important;
    }
    
    .dark .profile-card {
        background: white !important;
    }
    
    .dark label {
        color: #4a5568 !important;
    }
</style>

<div class="py-12 px-8 max-w-[900px] mx-auto relative z-10">
    
    <!-- Header Navegación -->
    <div class="flex items-center gap-4 mb-12">
        <a href="{{ route('team.show', $teamMember) }}" class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-gray-400 hover:text-white transition-colors">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h2 class="text-sm font-bold text-gray-900 uppercase tracking-[0.2em]">Editar Ficha de Colaborador</h2>
    </div>

    <form action="{{ route('team.update', $teamMember) }}" method="POST" class="profile-card rounded-3xl p-10 space-y-8" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="flex flex-col md:flex-row items-center gap-8 mb-4">
            <div class="w-24 h-24 rounded-3xl border border-gray-200 overflow-hidden bg-gray-50 shrink-0">
                @if($teamMember->photo)
                    <img src="{{ asset('storage/' . $teamMember->photo) }}" alt="" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                        <i class="fas fa-user text-3xl"></i>
                    </div>
                @endif
            </div>
            <div class="space-y-2 flex-1">
                <label class="text-[10px] font-bold uppercase tracking-widest">Cambiar Foto de Perfil</label>
                <input type="file" name="photo" accept="image/*"
                       class="w-full text-xs text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Nombre -->
            <div class="space-y-2">
                <label class="text-[10px] font-bold uppercase tracking-widest">Nombre Completo</label>
                <input type="text" name="name" value="{{ old('name', $teamMember->name) }}" required 
                       class="w-full input-field rounded-xl p-4 text-sm focus:outline-none">
            </div>

            <!-- Cargo -->
            <div class="space-y-2">
                <label class="text-[10px] font-bold uppercase tracking-widest">Cargo en la Agencia</label>
                <input type="text" name="position" value="{{ old('position', $teamMember->position) }}" required 
                       class="w-full input-field rounded-xl p-4 text-sm focus:outline-none">
            </div>

            <!-- Cédula -->
            <div class="space-y-2">
                <label class="text-[10px] font-bold uppercase tracking-widest">Cédula / Identificación</label>
                <input type="text" name="cedula" value="{{ old('cedula', $teamMember->cedula) }}" required 
                       class="w-full input-field rounded-xl p-4 text-sm focus:outline-none">
            </div>

            <!-- Fecha Nacimiento -->
            <div class="space-y-2">
                <label class="text-[10px] font-bold uppercase tracking-widest">Fecha de Nacimiento</label>
                <input type="date" name="birth_date" value="{{ old('birth_date', $teamMember->birth_date ? $teamMember->birth_date->format('Y-m-d') : '') }}" 
                       class="w-full input-field rounded-xl p-4 text-sm focus:outline-none">
            </div>

            <!-- Email -->
            <div class="space-y-2">
                <label class="text-[10px] font-bold uppercase tracking-widest">Correo Electrónico</label>
                <input type="email" name="email" value="{{ old('email', $teamMember->email) }}" 
                       class="w-full input-field rounded-xl p-4 text-sm focus:outline-none">
            </div>

            <!-- Teléfono -->
            <div class="space-y-2">
                <label class="text-[10px] font-bold uppercase tracking-widest">Teléfono Móvil</label>
                <input type="text" name="phone" value="{{ old('phone', $teamMember->phone) }}" 
                       class="w-full input-field rounded-xl p-4 text-sm focus:outline-none">
            </div>

            <!-- Dirección -->
            <div class="col-span-1 md:col-span-2 space-y-2">
                <label class="text-[10px] font-bold uppercase tracking-widest">Dirección de Residencia</label>
                <input type="text" name="address" value="{{ old('address', $teamMember->address) }}" 
                       class="w-full input-field rounded-xl p-4 text-sm focus:outline-none">
            </div>

            <!-- Salario -->
            <div class="space-y-2">
                <label class="text-[10px] font-bold uppercase tracking-widest">Asignación Salarial ($)</label>
                <input type="number" step="0.01" name="salary" value="{{ old('salary', $teamMember->salary) }}" required
                       class="w-full input-field rounded-xl p-4 text-sm focus:outline-none">
            </div>

            <!-- Rol -->
            <div class="space-y-2">
                <label class="text-[10px] font-bold uppercase tracking-widest">Rol del Sistema</label>
                <select name="role" required class="w-full input-field rounded-xl p-4 text-sm focus:outline-none">
                    <option value="colaborador" {{ (old('role', $teamMember->user->role ?? '') == 'colaborador') ? 'selected' : '' }}>Colaborador</option>
                    <option value="ceo" {{ (old('role', $teamMember->user->role ?? '') == 'ceo') ? 'selected' : '' }}>CEO</option>
                    <option value="rrhh" {{ (old('role', $teamMember->user->role ?? '') == 'rrhh') ? 'selected' : '' }}>Recursos Humanos</option>
                    <option value="contabilidad" {{ (old('role', $teamMember->user->role ?? '') == 'contabilidad') ? 'selected' : '' }}>Contabilidad</option>
                    <option value="admin" {{ (old('role', $teamMember->user->role ?? '') == 'admin') ? 'selected' : '' }}>Administrador (Soporte)</option>
                </select>
            </div>

            <!-- Detalles Bancarios -->
            <div class="col-span-1 md:col-span-2 space-y-2">
                <label class="text-[10px] font-bold uppercase tracking-widest">Información de Tesorería (Banco, Cuenta, Nequi)</label>
                <textarea name="bank_details" rows="3" 
                          class="w-full input-field rounded-xl p-4 text-sm focus:outline-none resize-none">{{ old('bank_details', $teamMember->bank_details) }}</textarea>
            </div>
        </div>

        <div class="pt-8 border-t border-gray-100 flex justify-end gap-4">
            <a href="{{ route('team.show', $teamMember) }}" class="py-4 px-8 rounded-xl bg-gray-50 border border-gray-200 text-xs font-bold text-gray-500 uppercase tracking-widest hover:bg-gray-100 transition-colors">
                Cancelar
            </a>
            <button type="submit" class="py-4 px-8 rounded-xl bg-black text-white font-bold text-xs uppercase tracking-widest hover:bg-gray-800 transition-colors">
                Guardar Cambios
            </button>
        </div>
    </form>

</div>
@endsection
