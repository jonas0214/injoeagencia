@extends('layouts.asana')

@section('content')
<div class="py-12 px-8 max-w-[800px] mx-auto">
    <div class="mb-12 border-b border-gray-200 dark:border-white/5 pb-8">
        <h1 class="text-3xl font-light text-gray-900 dark:text-white tracking-wide mb-1">
            {{ isset($project) ? 'Editar' : 'Nueva' }} <span class="font-medium text-gray-900 dark:text-white">Campaña / Proyecto</span>
        </h1>
        <p class="text-gray-500 text-xs font-medium tracking-[0.15em] uppercase">Define los objetivos y estructura inicial de tu proyecto</p>
    </div>

    <div class="bg-white dark:bg-white/[0.03] backdrop-blur-md border border-gray-200 dark:border-white/10 rounded-3xl p-8 shadow-sm dark:shadow-2xl transition-all duration-300">
        <form action="{{ isset($project) ? route('projects.update', $project) : route('projects.store') }}" method="POST" class="space-y-8">
            @csrf
            @if(isset($project))
                @method('PUT')
            @endif

            <div class="space-y-6">
                <!-- Nombre -->
                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Nombre del Proyecto</label>
                    <input type="text" name="name" value="{{ $project->name ?? '' }}" required 
                           class="w-full bg-gray-50 dark:bg-[#111] border border-gray-200 dark:border-white/10 rounded-xl p-4 text-sm text-gray-900 dark:text-white focus:ring-1 focus:ring-orange-500 outline-none"
                           placeholder="Ej: Lanzamiento Marca XYZ">
                </div>

                <!-- Descripción -->
                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Descripción / Objetivo</label>
                    <textarea name="description" rows="4" 
                              class="w-full bg-gray-50 dark:bg-[#111] border border-gray-200 dark:border-white/10 rounded-xl p-4 text-sm text-gray-900 dark:text-white focus:ring-1 focus:ring-orange-500 outline-none resize-none"
                              placeholder="Describe brevemente de qué trata este proyecto...">{{ $project->description ?? '' }}</textarea>
                </div>
            </div>

            <div class="flex gap-4 pt-4">
                <a href="{{ route('dashboard') }}" class="flex-1 py-4 rounded-2xl bg-gray-100 dark:bg-white/5 border border-gray-200 dark:border-white/10 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest text-center hover:bg-gray-200 dark:hover:bg-white/10 transition-all">Cancelar</a>
                <button type="submit" class="flex-[2] py-4 rounded-2xl bg-orange-600 text-white font-bold text-xs uppercase tracking-[0.2em] hover:bg-orange-700 transition-all shadow-xl shadow-orange-900/20">
                    {{ isset($project) ? 'Guardar Cambios' : 'Crear Proyecto' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
