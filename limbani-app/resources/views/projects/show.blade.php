@extends('layouts.asana')

@section('content')
<style>
    /* MODO ENFOQUE: Ocultar Sidebar y expandir contenido */
    aside, .sidebar, #sidebar, nav[class*="w-"] { display: none !important; }
    main, .main-content, div[class*="lg:col-span-"] { margin-left: 0 !important; width: 100% !important; max-width: 100% !important; grid-column: span 12 !important; }
    
    body {
        background-color: #0f1012 !important; /* Color de fondo corregido */
        font-family: 'Outfit', sans-serif;
        overflow: hidden; /* El scroll lo maneja el contenedor interno */
    }
    
    /* Scrollbar personalizada oscura */
    .custom-scroll::-webkit-scrollbar { width: 6px; }
    .custom-scroll::-webkit-scrollbar-track { background: transparent; }
    .custom-scroll::-webkit-scrollbar-thumb { background: #333; border-radius: 3px; }
    .custom-scroll::-webkit-scrollbar-thumb:hover { background: #ffaa00; }
</style>

<div x-data="projectManager()" class="flex h-screen w-full bg-[#0f1012] text-white overflow-hidden relative">

    <div class="flex-1 flex flex-col min-w-0 transition-all duration-300" 
         :class="openPanel ? 'mr-[450px]' : ''">
        
        <div class="px-8 py-6 border-b border-white/5 bg-[#0f1012] z-10 flex justify-between items-center shrink-0">
            <div class="flex items-center gap-4">
                <a href="{{ route('dashboard') }}" class="w-10 h-10 rounded-xl bg-[#1a1a1a] border border-white/10 flex items-center justify-center shadow-lg hover:bg-white/5 transition-colors text-gray-400">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div class="w-12 h-12 rounded-xl bg-[#1a1a1a] border border-white/10 flex items-center justify-center shadow-lg shadow-orange-900/10">
                    <i class="fas fa-layer-group text-orange-500 text-lg"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2 text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">
                        <span>Proyecto</span>
                        <i class="fas fa-chevron-right text-[8px] opacity-50"></i>
                        <span class="text-green-500">Activo</span>
                    </div>
                    <h1 class="text-3xl font-medium tracking-tight text-white">{{ $project->name }}</h1>
                </div>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="flex -space-x-2">
                    @foreach($project->tasks->flatMap->subtasks->pluck('user')->unique('id')->take(3) as $user)
                        <div class="w-8 h-8 rounded-full bg-[#1a1a1a] border border-[#0f1012] flex items-center justify-center text-xs text-gray-400 font-bold" title="{{ $user->name ?? 'Usuario' }}">
                            {{ substr($user->name ?? 'U', 0, 1) }}
                        </div>
                    @endforeach
                </div>
                <button class="bg-white text-black hover:bg-gray-200 px-5 py-2 rounded-full text-xs font-bold uppercase tracking-widest transition-colors">
                    <i class="fas fa-plus mr-2"></i> Nueva Sección
                </button>
            </div>
        </div>

        <div class="px-8 py-2 border-b border-white/5 bg-[#0f1012]/50 backdrop-blur-sm flex items-center gap-6 text-sm font-medium text-gray-400 shrink-0">
            <button class="text-white border-b-2 border-orange-500 pb-2 px-1">Lista</button>
            <button class="hover:text-white transition-colors pb-2 px-1">Tablero</button>
            <button class="hover:text-white transition-colors pb-2 px-1">Cronograma</button>
        </div>

        <div class="grid grid-cols-12 gap-4 px-8 py-3 border-b border-white/5 text-[10px] font-bold text-gray-500 uppercase tracking-wider bg-[#1a1a1a]/30 shrink-0">
            <div class="col-span-7">Tarea</div>
            <div class="col-span-3">Responsable</div>
            <div class="col-span-2 text-right">Fecha Entrega</div>
        </div>

        <div class="flex-1 overflow-y-auto custom-scroll p-8 space-y-2">
            
            @foreach($project->tasks as $section)
                <div x-data="{ expanded: true }" class="mb-8">
                    
                    <div class="group flex items-center gap-3 mb-3 cursor-pointer select-none py-2 px-2 rounded-lg hover:bg-white/5 transition-colors" @click="expanded = !expanded">
                        <div class="w-6 h-6 flex items-center justify-center rounded bg-white/5 text-gray-400 group-hover:text-white transition-colors">
                            <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="{ '-rotate-90': !expanded }"></i>
                        </div>
                        <h3 class="text-lg font-bold text-white group-hover:text-orange-500 transition-colors">{{ $section->title }}</h3>
                        <span class="text-xs text-gray-600 font-mono bg-[#1a1a1a] px-2 py-0.5 rounded border border-white/5">
                            {{-- Contamos solo las subtareas principales (parent_id null) --}}
                            {{ $section->subtasks->whereNull('parent_id')->count() }}
                        </span>
                    </div>

                    <div x-show="expanded" x-collapse class="space-y-1 pl-2">
                        
                        {{-- Filtramos solo las subtareas que NO tienen padre (Nivel 1 dentro de la sección) --}}
                        @foreach($section->subtasks->whereNull('parent_id') as $task)
                            <div @click="openTaskPanel({{ $task }})" 
                                 class="group relative grid grid-cols-12 gap-4 py-3 px-4 rounded-lg border border-transparent hover:bg-[#1a1a1a] hover:border-white/5 transition-all cursor-pointer items-center">
                                
                                <div class="col-span-7 flex items-center gap-4">
                                    <div class="relative flex items-center justify-center w-5 h-5 shrink-0" @click.stop>
                                        <input type="checkbox" 
                                               :checked="{{ $task->is_completed ? 'true' : 'false' }}"
                                               @change="toggleListStatus($event, {{ $task->id }})"
                                               class="peer appearance-none w-5 h-5 border border-gray-600 rounded bg-transparent checked:bg-green-500 checked:border-green-500 cursor-pointer transition-colors">
                                        <i class="fas fa-check text-black text-[10px] absolute opacity-0 peer-checked:opacity-100 pointer-events-none"></i>
                                    </div>
                                    <span class="text-sm text-gray-300 group-hover:text-white transition-colors font-medium truncate {{ $task->is_completed ? 'line-through opacity-50' : '' }}">
                                        {{ $task->title }}
                                    </span>
                                </div>

                                <div class="col-span-3 flex items-center">
                                    <div class="flex items-center gap-2 px-2 py-1 rounded-full bg-white/5 border border-white/5 group-hover:border-white/10 transition-colors">
                                        <div class="w-5 h-5 rounded-full bg-orange-500 flex items-center justify-center text-[8px] text-black font-bold">
                                            {{ substr(Auth::user()->name, 0, 1) }}
                                        </div>
                                        <span class="text-[10px] text-gray-400 truncate max-w-[80px]">{{ Auth::user()->name }}</span>
                                    </div>
                                </div>

                                <div class="col-span-2 text-right">
                                    <span class="text-xs font-mono text-gray-500 group-hover:text-orange-400 transition-colors">
                                        {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('M d') : '--/--' }}
                                    </span>
                                </div>
                            </div>
                            
                            {{-- SUBTAREAS DE NIVEL 2 (Indentadas) --}}
                            @foreach($task->children as $child)
                                <div @click="openTaskPanel({{ $child }})" 
                                     class="group relative grid grid-cols-12 gap-4 py-2 px-4 rounded-lg hover:bg-[#1a1a1a] transition-all cursor-pointer items-center ml-8 border-l border-white/5">
                                     
                                    <div class="col-span-7 flex items-center gap-4">
                                        <div class="absolute left-[-1px] top-1/2 w-4 h-[1px] bg-white/10"></div>
                                        
                                        <div class="relative flex items-center justify-center w-4 h-4 shrink-0" @click.stop>
                                            <input type="checkbox" 
                                                   :checked="{{ $child->is_completed ? 'true' : 'false' }}"
                                                   @change="toggleListStatus($event, {{ $child->id }})"
                                                   class="peer appearance-none w-4 h-4 border border-gray-600 rounded bg-transparent checked:bg-green-500 checked:border-green-500 cursor-pointer">
                                        </div>
                                        <span class="text-xs text-gray-400 group-hover:text-gray-200 truncate {{ $child->is_completed ? 'line-through opacity-50' : '' }}">
                                            {{ $child->title }}
                                        </span>
                                    </div>
                                    <div class="col-span-5"></div>
                                </div>
                            @endforeach

                        @endforeach

                        <div class="pl-4 pt-1">
                            <form action="{{ route('subtasks.store', $section) }}" method="POST" class="flex items-center gap-3 opacity-40 hover:opacity-100 transition-opacity group/add">
                                @csrf
                                <button type="submit" class="w-6 h-6 rounded flex items-center justify-center bg-white/5 group-hover/add:bg-orange-500 group-hover/add:text-black text-gray-400 transition-colors">
                                    <i class="fas fa-plus text-xs"></i>
                                </button>
                                <input type="text" name="title" placeholder="Agregar tarea a {{ $section->title }}..." 
                                       class="bg-transparent border-none text-sm text-gray-500 placeholder-gray-600 focus:ring-0 focus:text-white w-full h-8 p-0 focus:placeholder-gray-400 transition-colors">
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
            
            @if($project->tasks->isEmpty())
                <div class="text-center py-20 opacity-30">
                    <i class="fas fa-cubes text-4xl mb-4"></i>
                    <p>No hay secciones creadas.</p>
                </div>
            @endif

        </div>
    </div>

    <div x-show="openPanel" 
         x-transition:enter="transform transition ease-in-out duration-300"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transform transition ease-in-out duration-300"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         class="fixed inset-y-0 right-0 w-[450px] bg-[#1a1a1a]/95 backdrop-blur-xl border-l border-white/10 shadow-2xl z-50 flex flex-col"
         style="display: none;">
        
        <div class="px-6 py-4 border-b border-white/5 flex justify-between items-center bg-[#1a1a1a]">
            <div class="flex items-center gap-3">
                <div class="px-2 py-1 rounded bg-green-500/10 border border-green-500/20 text-green-500 text-[10px] font-bold uppercase tracking-wider">
                    <span x-text="currentTask.is_completed ? 'Completada' : 'Pendiente'"></span>
                </div>
            </div>
            <button @click="openPanel = false" class="text-gray-500 hover:text-white transition-colors">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto custom-scroll p-6 space-y-6">
            
            <div class="space-y-2">
                <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Título de la Tarea</label>
                <input type="text" x-model="currentTask.title" 
                       class="w-full bg-transparent border-none text-xl font-medium text-white placeholder-gray-600 focus:ring-0 p-0"
                       @change="updateTask()">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Responsable</label>
                    <div class="flex items-center gap-2 p-2 rounded bg-white/5 border border-white/5 cursor-pointer hover:border-white/20 transition-colors">
                        <div class="w-6 h-6 rounded-full bg-orange-500 flex items-center justify-center text-[10px] text-black font-bold">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <span class="text-xs text-gray-300">{{ Auth::user()->name }}</span>
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Fecha Entrega</label>
                    <input type="date" x-model="currentTask.due_date" 
                           class="w-full bg-white/5 border border-white/5 rounded p-2 text-xs text-gray-300 focus:ring-orange-500 focus:border-orange-500"
                           @change="updateTask()">
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Descripción</label>
                <textarea x-model="currentTask.description" rows="6" 
                          class="w-full bg-white/5 border border-white/5 rounded-lg p-4 text-sm text-gray-300 placeholder-gray-600 focus:ring-orange-500 focus:border-orange-500 resize-none"
                          placeholder="Añade una descripción detallada..."
                          @change="updateTask()"></textarea>
            </div>

            <div class="pt-6 border-t border-white/5">
                <button @click="togglePanelStatus()" 
                        class="w-full py-3 rounded-lg border border-white/10 font-medium text-sm transition-all hover:bg-white/5 flex items-center justify-center gap-2"
                        :class="currentTask.is_completed ? 'text-yellow-500 border-yellow-500/20' : 'text-green-500 border-green-500/20'">
                    <i class="fas" :class="currentTask.is_completed ? 'fa-undo' : 'fa-check'"></i>
                    <span x-text="currentTask.is_completed ? 'Marcar como Pendiente' : 'Marcar como Completada'"></span>
                </button>
            </div>

        </div>
    </div>

</div>

<script>
    function projectManager() {
        return {
            openPanel: false,
            currentTask: {},
            
            openTaskPanel(task) {
                // Prevenir que los valores null rompan el x-model
                this.currentTask = {
                    ...task,
                    due_date: task.due_date ? task.due_date.substring(0, 10) : ''
                };
                this.openPanel = true;
            },

            // Checkbox de la lista
            toggleListStatus(event, taskId) {
                const isChecked = event.target.checked;
                this.sendUpdate(taskId, { is_completed: isChecked });
            },

            // Botón del panel
            togglePanelStatus() {
                this.currentTask.is_completed = !this.currentTask.is_completed;
                this.sendUpdate(this.currentTask.id, { is_completed: this.currentTask.is_completed });
            },

            // Guardar cambios de texto/fecha
            updateTask() {
                if(!this.currentTask.id) return;
                this.sendUpdate(this.currentTask.id, {
                    title: this.currentTask.title,
                    description: this.currentTask.description,
                    due_date: this.currentTask.due_date
                });
            },

            // Función genérica de envío
            sendUpdate(id, data) {
                // IMPORTANTE: Asegúrate de que esta ruta exista en web.php: Route::put('/subtasks/{subtask}', ...)
                fetch(`/subtasks/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(data)
                })
                .then(res => {
                    if(!res.ok) throw new Error('Error al guardar');
                    return res.json();
                })
                .then(data => {
                    console.log('Actualizado correctamente');
                })
                .catch(err => console.error(err));
            }
        }
    }
</script>
@endsection