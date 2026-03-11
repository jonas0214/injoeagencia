@extends('layouts.asana')

@section('content')
<div class="py-12 px-4 md:px-8 max-w-3xl mx-auto">
    <div class="mb-12 border-b border-gray-200 dark:border-white/5 pb-8">
        <h1 class="text-3xl font-light text-gray-900 dark:text-white tracking-wide mb-1">
            {{ isset($project) ? 'Editar Proyecto' : 'Crear Nuevo Proyecto' }}
        </h1>
        <p class="text-gray-500 text-xs font-medium tracking-[0.15em] uppercase">
            {{ isset($project) ? 'Modifica los detalles del proyecto' : 'Define los detalles iniciales del nuevo proyecto o campaña' }}
        </p>
    </div>

    <div class="bg-white dark:bg-white/[0.03] backdrop-blur-md border border-gray-200 dark:border-white/10 rounded-3xl p-8 shadow-sm dark:shadow-2xl">
        <form action="{{ isset($project) ? route('projects.update', $project) : route('projects.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @if(isset($project))
                @method('PUT')
            @endif

            <div class="space-y-6">
                <!-- Nombre del Proyecto -->
                <div class="space-y-2">
                    <label for="name" class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Nombre del Proyecto</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $project->name ?? '') }}" required 
                           class="w-full bg-gray-50 dark:bg-[#111] border border-gray-200 dark:border-white/10 rounded-xl p-4 text-sm text-gray-900 dark:text-white focus:ring-1 focus:ring-orange-500 outline-none @error('name') border-red-500 @enderror"
                           placeholder="Ej: Lanzamiento Marca XYZ">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Descripción -->
                <div class="space-y-2">
                    <label for="description" class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Descripción</label>
                    <textarea name="description" id="description" rows="3"
                           class="w-full bg-gray-50 dark:bg-[#111] border border-gray-200 dark:border-white/10 rounded-xl p-4 text-sm text-gray-900 dark:text-white focus:ring-1 focus:ring-orange-500 outline-none @error('description') border-red-500 @enderror"
                           placeholder="Describe brevemente el objetivo del proyecto...">{{ old('description', $project->description ?? '') }}</textarea>
                    @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Logo -->
                <div class="space-y-2">
                    <label for="logo" class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Logo del Proyecto (Opcional)</label>
                    <input type="file" name="logo" id="logo" accept="image/*"
                           class="w-full bg-gray-50 dark:bg-[#111] border border-gray-200 dark:border-white/10 rounded-xl p-3 text-sm text-gray-900 dark:text-white focus:ring-1 focus:ring-orange-500 outline-none file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100 dark:file:bg-orange-900/20 dark:file:text-orange-400">
                    @error('logo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    @if(isset($project) && $project->logo)
                        <div class="mt-2 text-xs text-gray-500">Logo actual guardado. Sube uno nuevo para reemplazar.</div>
                    @endif
                </div>

                <!-- Plantilla a usar (Solo creación) -->
                @if(!isset($project))
                <div class="space-y-2">
                    <label for="template_id" class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Usar Plantilla de Tareas (Opcional)</label>
                    <select name="template_id" id="template_id" class="w-full bg-gray-50 dark:bg-[#111] border border-gray-200 dark:border-white/10 rounded-xl p-4 text-sm text-gray-900 dark:text-white focus:ring-1 focus:ring-orange-500 outline-none">
                        <option value="">-- Ninguna --</option>
                        @foreach($templates as $template)
                            <option value="{{ $template->id }}">{{ $template->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                
                <!-- Guardar como plantilla -->
                <div class="flex items-center gap-3 pt-2">
                    <input type="checkbox" name="is_template" id="is_template" value="1" {{ old('is_template', $project->is_template ?? false) ? 'checked' : '' }}
                           class="w-5 h-5 rounded border-gray-300 dark:border-white/10 text-orange-600 focus:ring-orange-500 dark:bg-[#111]">
                    <label for="is_template" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Guardar este proyecto como plantilla
                    </label>
                </div>
            </div>

            <div class="flex gap-4 pt-8 border-t border-gray-200 dark:border-white/10">
                <a href="{{ route('dashboard') }}" class="flex-1 py-4 rounded-2xl bg-gray-100 dark:bg-white/5 border border-gray-200 dark:border-white/10 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest text-center hover:bg-gray-200 dark:hover:bg-white/10 transition-all">Cancelar</a>
                <button type="submit" class="flex-[2] py-4 rounded-2xl bg-orange-600 text-white font-bold text-xs uppercase tracking-[0.2em] hover:bg-orange-700 transition-all shadow-xl shadow-orange-900/20">
                    {{ isset($project) ? 'Actualizar Proyecto' : 'Crear Proyecto' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection