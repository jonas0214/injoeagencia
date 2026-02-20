@extends('layouts.asana')

@section('content')
<style>
    @media (min-width: 768px) { aside { display: none !important; } main { margin-left: 0 !important; width: 100% !important; max-width: 100% !important; } }
    .dark body { background-color: #0f1012 !important; }
    body:not(.dark) { background-color: #f3f4f6 !important; }
    body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; overflow: hidden; -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; letter-spacing: -0.01em; }
</style>

<div class="flex h-screen w-full bg-white dark:bg-[#0f1012] text-gray-800 dark:text-white overflow-hidden relative" x-data="{ currentTab: 'list' }">

    <div class="flex-1 flex flex-col min-w-0 transition-all duration-300" :class="openPanel ? 'md:mr-[650px]' : ''">
        
        <div class="px-4 md:px-8 py-4 md:py-6 border-b border-gray-200 dark:border-white/5 bg-white dark:bg-[#0f1012] z-10 flex flex-col md:flex-row justify-between items-start md:items-center shrink-0 gap-4">
            <div class="flex items-center gap-3 md:gap-4 w-full md:w-auto">
                <a href="{{ route('dashboard') }}" class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-[#1a1a1a] border border-gray-200 dark:border-white/10 flex items-center justify-center shadow-sm hover:bg-gray-200 dark:hover:bg-white/5 transition-colors text-gray-400">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-xl md:text-3xl font-medium tracking-tight text-gray-900 dark:text-white truncate max-w-[200px] md:max-w-none">{{ $project->name }}</h1>
                </div>
            </div>
            
            @if(in_array(Auth::user()->role, ['admin', 'ceo']))
            <div class="flex items-center gap-4 w-full md:w-auto justify-between md:justify-end">
                <div x-data="{ addingSection: false, sectionTitle: '' }">
                    <button @click="addingSection = true; $nextTick(() => $refs.sectionInput.focus())" x-show="!addingSection" class="bg-gray-900 dark:bg-white text-white dark:text-black hover:bg-gray-800 dark:hover:bg-gray-200 px-5 py-2 rounded-full text-xs font-bold uppercase tracking-widest transition-colors shadow-lg">
                        <i class="fas fa-plus mr-2"></i> Nueva Sección
                    </button>
                    <div x-show="addingSection" class="flex items-center gap-2" style="display: none;">
                        <input type="text" x-model="sectionTitle" x-ref="sectionInput" @keydown.enter.prevent="fetch('/projects/{{ $project->id }}/tasks', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify({ title: sectionTitle }) }).then(() => window.location.reload())" placeholder="Nombre de la sección..." class="bg-gray-100 dark:bg-[#1a1a1a] border border-gray-300 dark:border-white/10 rounded-lg px-3 py-1.5 text-xs text-gray-900 dark:text-white focus:ring-1 focus:ring-orange-500 outline-none">
                        <button @click="addingSection = false" class="text-gray-500 hover:text-white p-1.5 transition-colors"><i class="fas fa-times text-[10px]"></i></button>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Pestañas de Proyecto -->
        <div class="px-4 md:px-8 border-b border-gray-200 dark:border-white/5 bg-white dark:bg-[#0f1012] flex gap-8 shrink-0 overflow-x-auto scrollbar-hide">
            <button @click="currentTab = 'list'; $dispatch('tab-changed', 'list')" :class="currentTab === 'list' ? 'text-orange-500 border-b-2 border-orange-500' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'" class="pb-4 pt-2 text-xs font-bold uppercase tracking-widest transition-all">Listado de Tareas</button>
            <button @click="currentTab = 'meta'; $dispatch('tab-changed', 'meta')" :class="currentTab === 'meta' ? 'text-orange-500 border-b-2 border-orange-500' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'" class="pb-4 pt-2 text-xs font-bold uppercase tracking-widest transition-all flex items-center gap-2">
                <i class="fab fa-facebook"></i> Programación Meta Ads
            </button>
        </div>

        <div x-show="currentTab === 'list'">
        <div class="hidden md:grid grid-cols-12 gap-4 px-8 py-3 border-b border-gray-200 dark:border-white/10 text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest bg-gray-50 dark:bg-[#111] shrink-0">
            <div class="col-span-7">Nombre de la Tarea</div>
            <div class="col-span-3">Responsable</div>
            <div class="col-span-2 text-right">Vencimiento</div>
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
                            
                            <div x-show="editingTitle" class="flex-1" style="display: none;">
                                <input type="text"
                                    x-model="newTitle"
                                    x-ref="titleInput"
                                    @keydown.enter="fetch('{{ url('/tasks') }}/{{ $section->id }}', { method: 'PUT', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }, body: JSON.stringify({ title: newTitle }) }).then(() => window.location.reload())"
                                    @keydown.escape="editingTitle = false"
                                    @click.away="editingTitle = false"
                                    class="bg-transparent border-none p-0 text-[11px] font-bold text-white uppercase tracking-[0.2em] focus:ring-0 outline-none w-full">
                            </div>
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
                        @foreach($section->subtasks->whereNull('parent_id') as $task)
                            <div x-data="{
                                showChildren: localStorage.getItem('task_children_{{ $task->id }}') === 'true' ? true : false,
                                toggleChildren() { this.showChildren = !this.showChildren; localStorage.setItem('task_children_{{ $task->id }}', this.showChildren); }
                            }" class="border-b border-gray-100 dark:border-white/[0.03]">
                                <div @click="openTaskPanel(@js($task), @js($section->title), '')" class="group grid grid-cols-12 gap-4 py-2 px-4 hover:bg-gray-50 dark:hover:bg-white/[0.03] transition-all cursor-pointer items-center">
                                    <div class="col-span-7 flex items-center gap-3">
                                        @if($task->children->count() > 0)
                                            <button @click.stop="toggleChildren()" class="w-4 h-4 flex items-center justify-center text-gray-600 hover:text-white transition-colors">
                                                <i class="fas fa-caret-right text-[10px] transition-transform" :class="showChildren ? 'rotate-90' : ''"></i>
                                            </button>
                                        @else <div class="w-4"></div> @endif
                                        <input type="checkbox" :checked="{{ $task->is_completed ? 'true' : 'false' }}" @change="fetch('{{ url('/subtasks') }}/{{ $task->id }}', { method: 'PUT', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify({ is_completed: $event.target.checked }) })" class="w-4 h-4 border-gray-600 rounded-sm bg-transparent checked:bg-green-500">
                                        <span class="text-[15px] font-medium text-gray-700 dark:text-gray-200 {{ $task->is_completed ? 'line-through opacity-40' : '' }}">{{ $task->title }}</span>
                                    </div>
                                    <div class="col-span-3">
                                        @if($task->team_member_id)
                                            <div class="flex items-center gap-2.5">
                                                @if($task->teamMember && $task->teamMember->photo)
                                                    <img src="{{ asset('storage/' . $task->teamMember->photo) }}" class="w-7 h-7 rounded-full object-cover border border-white/10">
                                                @else
                                                    <div class="w-7 h-7 rounded-full bg-orange-500 text-black flex items-center justify-center text-[10px] font-bold">{{ substr($task->teamMember->name ?? '?', 0, 1) }}</div>
                                                @endif
                                                <span class="text-[13px] text-gray-400 font-medium truncate max-w-[100px]">{{ $task->teamMember->name ?? '' }}</span>
                                            </div>
                                        @else
                                            <div class="flex items-center gap-2.5 text-gray-600"><div class="w-7 h-7 rounded-full border border-dashed border-gray-700 flex items-center justify-center text-[10px]"><i class="fas fa-user"></i></div><span class="text-[12px] italic">Sin asignar</span></div>
                                        @endif
                                    </div>
                                    <div class="col-span-2 text-right">
                                        <div class="flex flex-col items-end">
                                            <span class="text-[13px] font-medium text-gray-400">
                                                {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('d M, h:i A') : '--' }}
                                            </span>
                                            @if($task->due_date)
                                                <span class="text-[9px] font-bold uppercase tracking-tight {{ \Carbon\Carbon::parse($task->due_date)->isPast() ? 'text-red-500' : 'text-green-500' }}">
                                                    {{ \Carbon\Carbon::parse($task->due_date)->isPast() ? 'Vencida' : 'Pendiente' }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div x-show="showChildren" x-collapse>
                                    @foreach($task->children as $child)
                                        <div @click="openTaskPanel(@js($child), @js($section->title), @js($task->title))" class="group grid grid-cols-12 gap-4 py-1.5 px-4 ml-10 border-b border-gray-50 dark:border-white/[0.01] hover:bg-gray-50/50 dark:hover:bg-white/[0.02] cursor-pointer items-center">
                                            <div class="col-span-7 flex items-center gap-3">
                                                <input type="checkbox" :checked="{{ $child->is_completed ? 'true' : 'false' }}" class="w-3 h-3 border-gray-700 rounded-sm bg-transparent checked:bg-green-500/50">
                                                <span class="text-xs text-gray-500 {{ $child->is_completed ? 'line-through opacity-30' : '' }}">{{ $child->title }}</span>
                                            </div>
                                            <div class="col-span-3">
                                                @if($child->team_member_id)
                                                    <div class="flex items-center gap-2">
                                                        @if($child->teamMember && $child->teamMember->photo)
                                                            <img src="{{ asset('storage/' . $child->teamMember->photo) }}" class="w-5 h-5 rounded-full object-cover border border-white/10">
                                                        @else
                                                            <div class="w-5 h-5 rounded-full bg-orange-500/10 flex items-center justify-center text-[8px] text-orange-500 font-bold">{{ substr($child->teamMember->name ?? '?', 0, 1) }}</div>
                                                        @endif
                                                        <span class="text-[9px] text-gray-600 truncate">{{ $child->teamMember->name ?? '' }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="col-span-2 text-right opacity-0 group-hover:opacity-100">
                                                @if(Auth::user()->role !== 'colaborador')
                                                <button type="button" @click.stop="if(confirm('¿Borrar?')) fetch('{{ url('/subtasks') }}/{{ $child->id }}', { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } }).then(() => window.location.reload())" class="text-gray-600 hover:text-red-500 p-1"><i class="fas fa-trash-alt text-[10px]"></i></button>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                        @if(in_array(Auth::user()->role, ['admin', 'ceo']))
                        <form action="{{ route('subtasks.store', $section) }}" method="POST" class="pl-12 pt-2 flex items-center gap-4 opacity-40 hover:opacity-100 transition-opacity">@csrf<button type="submit" class="w-7 h-7 rounded-lg flex items-center justify-center bg-black/5 dark:bg-white/5 hover:bg-orange-500 hover:text-black transition-all"><i class="fas fa-plus text-xs"></i></button><input type="text" name="title" placeholder="Agregar tarea..." class="bg-transparent border-none text-[14px] text-gray-500 dark:text-gray-400 focus:ring-0 w-full"></form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        </div>

        <!-- Nueva Sección Meta Ads -->
        <div x-show="currentTab === 'meta'" style="display: none;" class="flex-1 overflow-y-auto custom-scroll p-4 md:p-8">
            <div class="max-w-6xl mx-auto space-y-8" x-data="{ isGenerating: false }">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center bg-orange-500/5 border border-orange-500/10 p-8 rounded-[2.5rem] gap-6 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-orange-500/5 rounded-full blur-3xl -mr-20 -mt-20"></div>
                    <div class="relative z-10">
                        <h3 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">Planificador Estratégico Meta Ads</h3>
                        <p class="text-sm text-gray-500 mt-1">Optimiza tu pauta con inteligencia artificial y flujos automáticos</p>
                    </div>
                    <div class="flex flex-wrap gap-3 relative z-10">
                        <button @click="isGenerating = true;
                                       fetch('{{ url('/projects/'.$project->id.'/generate-meta-strategy') }}', {
                                           method: 'POST',
                                           headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                                       }).then(() => window.location.reload())"
                                :disabled="isGenerating"
                                class="bg-orange-500 hover:bg-orange-600 text-black px-6 py-3 rounded-2xl text-xs font-black uppercase tracking-widest transition-all shadow-xl shadow-orange-500/20 disabled:opacity-50 flex items-center gap-2">
                            <i class="fas" :class="isGenerating ? 'fa-spinner animate-spin' : 'fa-magic'"></i>
                            <span x-text="isGenerating ? 'Generando...' : 'Generar Estrategia IA'"></span>
                        </button>
                        <button class="bg-gray-900 dark:bg-white text-white dark:text-black px-6 py-3 rounded-2xl text-xs font-black uppercase tracking-widest shadow-lg">
                            <i class="fas fa-rocket mr-2"></i> Lanzar Campaña
                        </button>
                    </div>
                </div>

                <!-- Tabla de Programación Meta -->
                <div class="bg-white dark:bg-white/[0.02] border border-gray-200 dark:border-white/5 rounded-[2.5rem] overflow-hidden shadow-sm transition-all">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-white/10 bg-gray-50 dark:bg-black/20 text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">
                                <th class="px-6 py-4">Fecha Pub</th>
                                <th class="px-6 py-4">Estrategia Meta</th>
                                <th class="px-6 py-4">Copywriting / Descripción</th>
                                <th class="px-6 py-4">Estado / Arte</th>
                                <th class="px-6 py-4 text-right">Inversión Sugerida</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                            @php
                                $metaSection = $project->tasks->where('title', 'PROGRAMACIÓN META ADS')->first();
                                $metaTasks = $metaSection ? $metaSection->subtasks->sortBy('due_date') : collect();
                            @endphp
                            @forelse($metaTasks as $task)
                            <tr class="group hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-all cursor-pointer border-l-4 border-transparent hover:border-orange-500" @click="openTaskPanel(@js($task), 'META ADS', '')">
                                <td class="px-6 py-6">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-black text-gray-800 dark:text-white">{{ $task->due_date ? $task->due_date->format('d M') : 'Pendiente' }}</span>
                                        <span class="text-[9px] text-gray-400 dark:text-gray-500 uppercase font-bold">{{ $task->due_date ? $task->due_date->locale('es')->dayName : 'Sin programar' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-6">
                                    <div class="space-y-2">
                                        <span class="px-2 py-0.5 rounded bg-blue-500/10 text-blue-500 text-[8px] font-black uppercase tracking-tighter">
                                            {{ str_contains(strtolower($task->title), 'video') || str_contains(strtolower($task->title), 'reel') ? 'VIDEO / REEL' : 'ESTÁTICO / CARROUSEL' }}
                                        </span>
                                        <p class="text-sm font-bold text-gray-700 dark:text-gray-200 leading-tight">{{ $task->title }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-6 max-w-xs">
                                    <div class="space-y-2">
                                        <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2 leading-relaxed">{{ $task->description ?? 'Haz clic para añadir el copy del anuncio...' }}</p>
                                        @if($task->ai_suggestion)
                                            <div class="flex items-center gap-1.5 text-orange-500 text-[9px] font-black uppercase tracking-tighter">
                                                <i class="fas fa-magic animate-pulse"></i> Sugerencia IA Activa
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-6">
                                    <div class="flex flex-col gap-2">
                                        <div class="flex items-center gap-2">
                                            <div class="w-1.5 h-1.5 rounded-full {{ $task->is_completed ? 'bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.5)]' : 'bg-yellow-500 shadow-[0_0_8px_rgba(234,179,8,0.5)]' }}"></div>
                                            <span class="text-[10px] font-black {{ $task->is_completed ? 'text-green-600 dark:text-green-500' : 'text-yellow-600 dark:text-yellow-500' }} uppercase tracking-widest">
                                                {{ $task->is_completed ? 'Listo para publicar' : 'En producción' }}
                                            </span>
                                        </div>
                                        @if($task->attachments->count() > 0)
                                            <div class="flex -space-x-1">
                                                @foreach($task->attachments->take(3) as $att)
                                                    <div class="w-5 h-5 rounded border border-white/10 bg-gray-800 overflow-hidden">
                                                        <i class="fas fa-image text-[8px] flex items-center justify-center h-full text-gray-500"></i>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-6 text-right font-mono">
                                    <div class="flex flex-col items-end">
                                        <span class="text-white bg-gray-900 dark:bg-orange-500/20 px-2 py-0.5 rounded text-[10px] font-black mb-1 leading-none tracking-tighter">${{ number_format($task->position * 5000 + 25000) }}</span>
                                        <span class="text-[9px] text-gray-400 font-bold uppercase tracking-widest">Presupuesto Diario</span>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-gray-500 italic text-xs uppercase tracking-widest">No hay programación cargada para este proyecto</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-blue-500/5 border border-blue-500/10 p-6 rounded-3xl">
                        <p class="text-[10px] font-bold text-blue-600 dark:text-blue-400 uppercase mb-2">Presupuesto Asignado</p>
                        <p class="text-2xl font-black text-gray-900 dark:text-white">$1,500,000</p>
                    </div>
                    <div class="bg-orange-500/5 border border-orange-500/10 p-6 rounded-3xl">
                        <p class="text-[10px] font-bold text-orange-600 dark:text-orange-400 uppercase mb-2">Alcance Estimado</p>
                        <p class="text-2xl font-black text-gray-900 dark:text-white">45,000 - 80,000</p>
                    </div>
                    <div class="bg-green-500/5 border border-green-500/10 p-6 rounded-3xl">
                        <p class="text-[10px] font-bold text-green-600 dark:text-green-400 uppercase mb-2">Conversiones Meta</p>
                        <p class="text-2xl font-black text-gray-900 dark:text-white">124 Leads</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
