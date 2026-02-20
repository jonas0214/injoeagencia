@extends('layouts.asana')

@section('content')
<style>
    @media (min-width: 768px) { aside { display: none !important; } main { margin-left: 0 !important; width: 100% !important; max-width: 100% !important; } }
    body { background-color: #0f1012 !important; font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; overflow: hidden; -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; letter-spacing: -0.01em; }
</style>

<div class="flex h-screen w-full bg-[#0f1012] text-white overflow-hidden relative">

    <div class="flex-1 flex flex-col min-w-0 transition-all duration-300" :class="openPanel ? 'md:mr-[650px]' : ''">
        
        <div class="px-4 md:px-8 py-4 md:py-6 border-b border-white/5 bg-[#0f1012] z-10 flex flex-col md:flex-row justify-between items-start md:items-center shrink-0 gap-4">
            <div class="flex items-center gap-3 md:gap-4 w-full md:w-auto">
                <a href="{{ route('dashboard') }}" class="w-10 h-10 rounded-xl bg-[#1a1a1a] border border-white/10 flex items-center justify-center shadow-lg hover:bg-white/5 transition-colors text-gray-400">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-xl md:text-3xl font-medium tracking-tight text-white truncate max-w-[200px] md:max-w-none">{{ $project->name }}</h1>
                </div>
            </div>
            
            @if(in_array(Auth::user()->role, ['admin', 'ceo']))
            <div class="flex items-center gap-4 w-full md:w-auto justify-between md:justify-end">
                <div x-data="{ addingSection: false, sectionTitle: '' }">
                    <button @click="addingSection = true; $nextTick(() => $refs.sectionInput.focus())" x-show="!addingSection" class="bg-white text-black hover:bg-gray-200 px-5 py-2 rounded-full text-xs font-bold uppercase tracking-widest transition-colors">
                        <i class="fas fa-plus mr-2"></i> Nueva Sección
                    </button>
                    <div x-show="addingSection" class="flex items-center gap-2" style="display: none;">
                        <input type="text" x-model="sectionTitle" x-ref="sectionInput" @keydown.enter.prevent="fetch('/projects/{{ $project->id }}/tasks', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify({ title: sectionTitle }) }).then(() => window.location.reload())" placeholder="Nombre de la sección..." class="bg-[#1a1a1a] border border-white/10 rounded-lg px-3 py-1.5 text-xs text-white focus:ring-1 focus:ring-orange-500 outline-none">
                        <button @click="addingSection = false" class="text-gray-500 hover:text-white p-1.5 transition-colors"><i class="fas fa-times text-[10px]"></i></button>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="hidden md:grid grid-cols-12 gap-4 px-8 py-3 border-b border-white/10 text-[10px] font-bold text-gray-400 uppercase tracking-widest bg-[#111] shrink-0">
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
                            }" class="border-b border-white/[0.03]">
                                <div @click="openTaskPanel(@js($task), @js($section->title), '')" class="group grid grid-cols-12 gap-4 py-2 px-4 hover:bg-white/[0.03] transition-all cursor-pointer items-center">
                                    <div class="col-span-7 flex items-center gap-3">
                                        @if($task->children->count() > 0)
                                            <button @click.stop="toggleChildren()" class="w-4 h-4 flex items-center justify-center text-gray-600 hover:text-white transition-colors">
                                                <i class="fas fa-caret-right text-[10px] transition-transform" :class="showChildren ? 'rotate-90' : ''"></i>
                                            </button>
                                        @else <div class="w-4"></div> @endif
                                        <input type="checkbox" :checked="{{ $task->is_completed ? 'true' : 'false' }}" @change="fetch('{{ url('/subtasks') }}/{{ $task->id }}', { method: 'PUT', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify({ is_completed: $event.target.checked }) })" class="w-4 h-4 border-gray-600 rounded-sm bg-transparent checked:bg-green-500">
                                        <span class="text-[15px] font-medium text-gray-200 {{ $task->is_completed ? 'line-through opacity-40' : '' }}">{{ $task->title }}</span>
                                    </div>
                                    <div class="col-span-3">
                                        @if($task->team_member_id)
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-7 h-7 rounded-full bg-orange-500 text-black flex items-center justify-center text-[10px] font-bold">{{ substr($task->teamMember->name ?? '?', 0, 1) }}</div>
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
                                        <div @click="openTaskPanel(@js($child), @js($section->title), @js($task->title))" class="group grid grid-cols-12 gap-4 py-1.5 px-4 ml-10 border-b border-white/[0.01] hover:bg-white/[0.02] cursor-pointer items-center">
                                            <div class="col-span-7 flex items-center gap-3">
                                                <input type="checkbox" :checked="{{ $child->is_completed ? 'true' : 'false' }}" class="w-3 h-3 border-gray-700 rounded-sm bg-transparent checked:bg-green-500/50">
                                                <span class="text-xs text-gray-500 {{ $child->is_completed ? 'line-through opacity-30' : '' }}">{{ $child->title }}</span>
                                            </div>
                                            <div class="col-span-3">
                                                @if($child->team_member_id)
                                                    <div class="flex items-center gap-2"><div class="w-5 h-5 rounded-full bg-orange-500/10 flex items-center justify-center text-[8px] text-orange-500 font-bold">{{ substr($child->teamMember->name ?? '?', 0, 1) }}</div><span class="text-[9px] text-gray-600 truncate">{{ $child->teamMember->name ?? '' }}</span></div>
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
                        <form action="{{ route('subtasks.store', $section) }}" method="POST" class="pl-12 pt-2 flex items-center gap-4 opacity-40 hover:opacity-100 transition-opacity">@csrf<button type="submit" class="w-7 h-7 rounded-lg flex items-center justify-center bg-white/5 hover:bg-orange-500 hover:text-black transition-all"><i class="fas fa-plus text-xs"></i></button><input type="text" name="title" placeholder="Agregar tarea..." class="bg-transparent border-none text-[14px] text-gray-400 focus:ring-0 w-full"></form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
