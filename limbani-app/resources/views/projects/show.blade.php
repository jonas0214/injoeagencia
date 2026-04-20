@extends('layouts.asana')

@section('content')
<style>
    @media (min-width: 768px) { aside { display: none !important; } main { margin-left: 0 !important; width: 100% !important; max-width: 100% !important; } }
    .dark body { background-color: #0f1012 !important; }
    body:not(.dark) { background-color: #f3f4f6 !important; }
    body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; letter-spacing: -0.01em; }
</style>

<div class="flex h-full w-full bg-white dark:bg-[#0f1012] text-gray-800 dark:text-white overflow-hidden relative" x-data="{ currentTab: 'list' }">

    <div class="flex-1 flex flex-col min-w-0 transition-all duration-300" :class="openPanel ? 'md:mr-[650px]' : ''">
        
        <div class="px-4 md:px-8 py-4 md:py-6 border-b border-gray-200 dark:border-white/5 bg-white dark:bg-[#0f1012] z-10 flex flex-col md:flex-row justify-between items-start md:items-center shrink-0 gap-4">
            <div class="flex items-center gap-3 md:gap-4 w-full md:w-auto">
                <a href="{{ route('dashboard') }}" class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-[#1a1a1a] border border-gray-200 dark:border-white/10 flex items-center justify-center shadow-sm hover:bg-gray-200 dark:hover:bg-white/5 transition-colors text-gray-400">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div class="flex items-center gap-3 md:gap-4">
                    @if($project->logo)
                        <div class="h-10 md:h-12 min-w-[2.5rem] md:min-w-[3rem] max-w-[10rem] px-2 rounded-xl bg-white dark:bg-orange-500/10 border border-gray-200 dark:border-white/10 flex items-center justify-center overflow-hidden shadow-sm">
                            <img src="{{ asset('storage/' . $project->logo) }}" alt="{{ $project->name }}" class="h-full w-auto object-contain py-1">
                        </div>
                    @else
                        <div class="h-10 md:h-12 min-w-[2.5rem] md:min-w-[3rem] px-2 rounded-xl bg-white dark:bg-orange-500/10 border border-gray-200 dark:border-white/10 flex items-center justify-center text-orange-500 shadow-sm">
                            <i class="fas fa-layer-group text-lg md:text-xl"></i>
                        </div>
                    @endif
                    <div>
                        <h1 class="text-xl md:text-3xl font-medium tracking-tight text-gray-900 dark:text-white truncate max-w-[200px] md:max-w-none">{{ $project->name }}</h1>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center gap-4 w-full md:w-auto justify-between md:justify-end">
                <x-notification-center />
                
                @if(in_array(Auth::user()->role, ['admin', 'ceo']))
                <div x-data="{ addingSection: false }">
                    <button type="button" @click="addingSection = true; $nextTick(() => $refs.sectionInput.focus())" x-show="!addingSection" class="bg-gray-900 dark:bg-white text-white dark:text-black hover:bg-gray-800 dark:hover:bg-gray-200 px-5 py-2 rounded-full text-xs font-bold uppercase tracking-widest transition-colors shadow-lg">
                        <i class="fas fa-plus mr-2"></i> Nueva Sección
                    </button>
                    <form action="{{ route('tasks.store', $project) }}" method="POST" x-show="addingSection" class="flex items-center gap-2" style="display: none;">
                        @csrf
                        <input type="text" name="title" x-ref="sectionInput" placeholder="Nombre de la sección..." required class="bg-gray-100 dark:bg-[#1a1a1a] border border-gray-300 dark:border-white/10 rounded-lg px-3 py-1.5 text-xs text-gray-900 dark:text-white focus:ring-1 focus:ring-orange-500 outline-none">
                        <button type="submit" class="text-green-500 hover:text-green-400 p-1.5 transition-colors" title="Guardar"><i class="fas fa-check text-xs"></i></button>
                        <button type="button" @click="addingSection = false" class="text-gray-500 hover:text-red-400 p-1.5 transition-colors" title="Cancelar"><i class="fas fa-times text-xs"></i></button>
                    </form>
                </div>
                @endif
            </div>
        </div>

        <!-- Pestañas de Proyecto -->
        <div class="px-4 md:px-8 border-b border-gray-200 dark:border-white/5 bg-white dark:bg-[#0f1012] flex gap-8 shrink-0 overflow-x-auto scrollbar-hide">
            <button @click="currentTab = 'list'; $dispatch('tab-changed', 'list')" :class="currentTab === 'list' ? 'text-orange-500 border-b-2 border-orange-500' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'" class="pb-4 pt-2 text-xs font-bold uppercase tracking-widest transition-all">Listado de Tareas</button>
            
            @if(in_array(Auth::user()->role, ['admin', 'ceo']))
            <button @click="currentTab = 'brief'; $dispatch('tab-changed', 'brief')" :class="currentTab === 'brief' ? 'text-orange-500 border-b-2 border-orange-500' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'" class="pb-4 pt-2 text-xs font-bold uppercase tracking-widest transition-all flex items-center gap-2">
                <i class="fas fa-file-alt"></i> Brief del Cliente
            </button>
            @endif
        </div>

        <div x-show="currentTab === 'list'" class="flex-1 flex flex-col min-h-0">
        <div class="hidden md:grid grid-cols-12 gap-4 px-8 py-3 border-b border-gray-200 dark:border-white/10 text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest bg-gray-50 dark:bg-[#111] shrink-0">
            <div class="col-span-7">Nombre de la Tarea</div>
            <div class="col-span-2">Responsable</div>
            <div class="col-span-3 text-right">Cronograma</div>
        </div>

        <div class="flex-1 overflow-y-auto custom-scroll p-4 md:p-8 space-y-4">
            @foreach($project->tasks as $section)
                <div x-data="{
                    expanded: localStorage.getItem('section_{{ $section->id }}') === 'false' ? false : true,
                    toggle() { this.expanded = !this.expanded; localStorage.setItem('section_{{ $section->id }}', this.expanded); }
                }" class="mb-10">
                    <div class="flex items-center gap-3 mb-4 group/sec relative" x-data="{ editingTitle: false, newTitle: '{{ addslashes($section->title) }}' }">
                        <div class="flex items-center gap-3 flex-1">
                            <i @click="toggle()" class="fas fa-caret-down text-gray-500 transition-transform cursor-pointer" :class="{ '-rotate-90': !expanded }"></i>
                            
                            <div x-show="!editingTitle" class="flex-1 flex items-center">
                                <h3 @click="if('{{ Auth::user()->role }}' !== 'colaborador') { editingTitle = true; $nextTick(() => $refs.titleInput.focus()); }" class="text-[11px] font-bold text-gray-500 uppercase tracking-[0.2em] group-hover/sec:text-orange-500 transition-colors cursor-pointer">{{ $section->title }}</h3>
                                <div @click="toggle()" class="h-[1px] flex-1 bg-white/5 ml-4 cursor-pointer"></div>
                            </div>
                            
                            <form action="{{ url('/tasks') }}/{{ $section->id }}" method="POST" x-show="editingTitle" class="flex-1 flex items-center gap-2 bg-gray-50 dark:bg-[#1a1a1a] px-2 py-1 rounded-lg border border-gray-200 dark:border-white/5" style="display: none;">
                                @csrf
                                @method('PUT')
                                <input type="text"
                                    name="title"
                                    value="{{ $section->title }}"
                                    x-ref="titleInput"
                                    @keydown.escape="editingTitle = false;"
                                    class="bg-transparent border-none p-0 text-[11px] font-bold text-gray-900 dark:text-white uppercase tracking-[0.2em] focus:ring-0 outline-none w-full">
                                <button type="submit" class="text-green-500 hover:text-green-600 dark:hover:text-green-400 p-1" title="Guardar"><i class="fas fa-check text-[10px]"></i></button>
                                <button type="button" @click="editingTitle = false;" class="text-gray-400 hover:text-red-500 p-1" title="Cancelar"><i class="fas fa-times text-[10px]"></i></button>
                            </form>
                        </div>

                        @if(in_array(Auth::user()->role, ['admin', 'ceo']))
                        <div class="flex items-center gap-1 opacity-0 group-hover/sec:opacity-100 transition-opacity bg-[#1a1a1a] px-2 rounded-lg border border-white/5 shadow-xl">
                            <button type="button" @click="fetch('{{ route('tasks.move', $section) }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify({ direction: 'up' }) }).then(() => window.location.reload())" class="text-gray-500 hover:text-white p-1.5" title="Subir"><i class="fas fa-chevron-up text-[9px]"></i></button>
                            <button type="button" @click="fetch('{{ route('tasks.move', $section) }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify({ direction: 'down' }) }).then(() => window.location.reload())" class="text-gray-500 hover:text-white p-1.5" title="Bajar"><i class="fas fa-chevron-down text-[9px]"></i></button>
                            <div class="w-[1px] h-3 bg-white/10 mx-1"></div>
                            <button type="button" @click="fetch('{{ route('tasks.duplicate', $section) }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } }).then(() => window.location.reload())" class="text-gray-500 hover:text-orange-500 p-1.5" title="Duplicar Sección"><i class="fas fa-copy text-[9px]"></i></button>
                            <button type="button" @click="if(confirm('¿Borrar sección?')) fetch('{{ url('/tasks') }}/{{ $section->id }}', { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } }).then(() => window.location.reload())" class="text-gray-500 hover:text-red-500 p-1.5" title="Borrar Sección"><i class="fas fa-trash-alt text-[9px]"></i></button>
                        </div>
                        @endif
                    </div>

                    <div x-show="expanded" x-collapse class="space-y-[1px]">
                        @include('projects._subtask_recursive', ['subtasks' => $section->subtasks->whereNull('parent_id'), 'level' => 0, 'section' => $section])
                        @if(in_array(Auth::user()->role, ['admin', 'ceo']))
                        <form action="{{ route('subtasks.store', $section) }}" method="POST" class="pl-12 pt-2 flex items-center gap-4 opacity-40 hover:opacity-100 transition-opacity">@csrf<button type="submit" class="w-7 h-7 rounded-lg flex items-center justify-center bg-black/5 dark:bg-white/5 hover:bg-orange-500 hover:text-black transition-all" title="Añadir tarea"><i class="fas fa-plus text-xs"></i></button><input type="text" name="title" placeholder="Agregar tarea..." required class="bg-transparent border-none text-[14px] text-gray-800 dark:text-gray-400 focus:ring-0 w-full"></form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        </div>

        <!-- Nueva Sección Meta Ads (Oculto) -->

        <!-- Sección Brief del Cliente -->
        <div x-show="currentTab === 'brief'" style="display: none;" class="flex-1 overflow-y-auto custom-scroll p-4 md:p-8">
            <div class="max-w-6xl mx-auto">
                @php
                    $brief = $project->brief;
                    $hasBrief = $brief ? true : false;
                @endphp
                
                <div class="bg-white dark:bg-white/[0.02] border border-gray-200 dark:border-white/10 rounded-[2.5rem] p-8 mb-8">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                        <div>
                            <h3 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">Brief del Cliente</h3>
                            <p class="text-sm text-gray-500 mt-1">Formulario dinámico para levantar requerimientos y planificar estrategias</p>
                        </div>
                        
                        <div class="flex flex-wrap gap-3">
                            @if($hasBrief)
                                <div class="px-4 py-2 rounded-full text-xs font-bold uppercase tracking-widest
                                    @if($brief->status === 'draft') bg-gray-500/10 text-gray-500
                                    @elseif($brief->status === 'submitted') bg-blue-500/10 text-blue-500
                                    @elseif($brief->status === 'reviewed') bg-yellow-500/10 text-yellow-500
                                    @elseif($brief->status === 'approved') bg-green-500/10 text-green-500
                                    @endif">
                                    {{ $brief->status === 'draft' ? 'Borrador' :
                                       ($brief->status === 'submitted' ? 'Enviado' :
                                       ($brief->status === 'reviewed' ? 'Revisado' : 'Aprobado')) }}
                                </div>
                            @endif
                            
                            <a href="{{ route('briefs.edit', $project) }}"
                                class="bg-orange-500 hover:bg-orange-600 text-black px-6 py-3 rounded-2xl text-xs font-black uppercase tracking-widest transition-all shadow-xl shadow-orange-500/20 flex items-center gap-2">
                                <i class="fas fa-edit"></i>
                                {{ $hasBrief ? 'Editar Brief' : 'Crear Brief' }}
                            </a>
                            
                            @if($hasBrief && $brief->status !== 'draft')
                            <a href="{{ route('briefs.show', $project) }}"
                                class="bg-gray-900 dark:bg-white text-white dark:text-black hover:bg-gray-800 dark:hover:bg-gray-200 px-6 py-3 rounded-2xl text-xs font-black uppercase tracking-widest shadow-lg flex items-center gap-2">
                                <i class="fas fa-eye"></i> Ver Brief
                            </a>
                            @endif
                        </div>
                    </div>
                </div>

                @if($hasBrief && is_array($brief->answers))
                <!-- Resumen del Brief (20 Preguntas) -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white dark:bg-white/[0.02] border border-gray-200 dark:border-white/10 rounded-3xl p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-8 h-8 rounded-xl bg-orange-500/10 flex items-center justify-center text-orange-500 text-sm">
                                <i class="fas fa-bullseye"></i>
                            </div>
                            <h4 class="text-xs font-bold uppercase tracking-widest text-gray-400">Objetivo Principal</h4>
                        </div>
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-200">
                            {{ $brief->answers['q3'] ?? 'No definido' }}
                        </p>
                    </div>
                    
                    <div class="bg-white dark:bg-white/[0.02] border border-gray-200 dark:border-white/10 rounded-3xl p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-8 h-8 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-500 text-sm">
                                <i class="fas fa-comment"></i>
                            </div>
                            <h4 class="text-xs font-bold uppercase tracking-widest text-gray-400">Mensaje Clave</h4>
                        </div>
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-200 truncate">
                            {{ Str::limit($brief->answers['q4'] ?? 'No definido', 60) }}
                        </p>
                    </div>

                    <div class="bg-white dark:bg-white/[0.02] border border-gray-200 dark:border-white/10 rounded-3xl p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-8 h-8 rounded-xl bg-purple-500/10 flex items-center justify-center text-purple-500 text-sm">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <h4 class="text-xs font-bold uppercase tracking-widest text-gray-400">Prioridad Máxima</h4>
                        </div>
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-200 truncate">
                            {{ Str::limit($brief->answers['q7'] ?? 'No definida', 60) }}
                        </p>
                    </div>
                </div>

                <div class="bg-white dark:bg-white/[0.02] border border-gray-200 dark:border-white/10 rounded-3xl p-8 mb-8">
                    <h4 class="text-xs font-bold uppercase tracking-[0.2em] text-gray-500 mb-6">Prioridades del Mes</h4>
                    <div class="text-lg font-medium text-gray-900 dark:text-white leading-relaxed">
                        @if(!empty($brief->answers['q1']))
                            {!! nl2br(e($brief->answers['q1'])) !!}
                        @else
                            <span class="text-gray-500 italic">Cliente no ha especificado prioridades aún.</span>
                        @endif
                    </div>
                    
                    <div class="mt-8 pt-8 border-t border-gray-200 dark:border-white/5 flex flex-wrap gap-8">
                        <div>
                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Presupuesto Pauta</p>
                            <p class="text-sm font-bold text-orange-500">${{ number_format((float)($brief->answers['q17'] ?? 0), 2) }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Última edicion</p>
                            <p class="text-sm font-medium">{{ $brief->updated_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>
                @else
                <!-- Estado sin brief o datos corruptos -->
                <div class="bg-white dark:bg-white/[0.02] border border-gray-200 dark:border-white/10 rounded-[2.5rem] p-12 text-center">
                    <div class="w-20 h-20 rounded-2xl bg-orange-500/10 flex items-center justify-center text-orange-500 mx-auto mb-6">
                        <i class="fas fa-file-alt text-2xl"></i>
                    </div>
                    <h4 class="text-xl font-medium text-gray-900 dark:text-white mb-3">{{ $hasBrief ? 'Configuración de Brief Requerida' : 'No hay brief creado' }}</h4>
                    <p class="text-gray-500 dark:text-gray-400 mb-6 max-w-md mx-auto">
                        {{ $hasBrief ? 'Tus datos del brief necesitan ser actualizados al nuevo formato estratégico.' : 'Crea un brief para este proyecto para levantar requerimientos del cliente, definir objetivos y planificar la estrategia.' }}
                    </p>
                    <a href="{{ route('briefs.edit', $project) }}"
                        class="bg-orange-500 hover:bg-orange-600 text-black px-8 py-4 rounded-2xl text-sm font-black uppercase tracking-widest transition-all shadow-xl shadow-orange-500/20 inline-flex items-center gap-3">
                        <i class="fas fa-{{ $hasBrief ? 'sync' : 'plus' }}"></i>
                        {{ $hasBrief ? 'Actualizar Formato de Brief' : 'Crear Brief del Cliente' }}
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
