@extends('layouts.asana')

@section('content')
<style>
    body {
        min-height: 100vh;
    }
    body.dark {
        background: linear-gradient(to bottom, #111827, #000000) !important;
    }
    body:not(.dark) {
        background: #f8f9fa !important;
    }
</style>
<div class="max-w-[1600px] mx-auto px-8 py-12">
    
    <!-- Header Minimalista -->
    <div class="mb-16 flex justify-between items-end border-b border-gray-200 dark:border-white/5 pb-8">
        <div>
            <h1 class="text-4xl font-light text-gray-800 dark:text-white tracking-wide mb-1">
                Bienvenido, <span class="font-medium text-gray-900 dark:text-white">{{ Auth::user()->name }}</span>
            </h1>
            <p class="text-gray-500 text-xs font-medium tracking-[0.15em] uppercase">
                {{ \Carbon\Carbon::now()->locale('es')->isoFormat('dddd, D [de] MMMM, YYYY') }}
            </p>
        </div>
        @if(in_array(Auth::user()->role, ['admin', 'ceo']))
        <a href="{{ route('projects.create') }}" class="bg-gray-900 dark:bg-white text-white dark:text-black hover:bg-gray-800 dark:hover:bg-gray-200 transition-colors px-6 py-2.5 rounded-full font-medium text-xs uppercase tracking-widest">
            <i class="fas fa-plus mr-2 text-[10px]"></i> Quick Action
        </a>
        @endif
    </div>

    <!-- Pestañas de Navegación Estilo Asana -->
    <div class="flex items-center gap-8 border-b border-gray-200 dark:border-white/5 mb-10 text-sm font-medium">
        <a href="{{ route('dashboard') }}" class="pb-4 {{ request()->routeIs('dashboard') ? 'text-gray-900 dark:text-white border-b-2 border-orange-500' : 'text-gray-500 hover:text-gray-300 transition-colors' }}">
            Proyectos
        </a>
        @if(in_array(Auth::user()->role, ['admin', 'ceo', 'rrhh', 'contabilidad']))
        <a href="{{ route('team.index') }}" class="pb-4 {{ request()->routeIs('team.index') ? 'text-gray-900 dark:text-white border-b-2 border-orange-500' : 'text-gray-500 hover:text-gray-300 transition-colors' }}">
            Equipo & Nómina
        </a>
        <a href="#" class="pb-4 text-gray-500 hover:text-gray-300 transition-colors opacity-50 cursor-not-allowed">
            Informes
        </a>
        @endif
        <a href="{{ route('billing.index') }}" class="pb-4 {{ request()->routeIs('billing.index') ? 'text-gray-900 dark:text-white border-b-2 border-orange-500' : 'text-gray-500 hover:text-gray-300 transition-colors' }}">
            Cuentas de Cobro
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
        
        <!-- COLUMNA PRINCIPAL: PROYECTOS (col-span-8) -->
        <div class="lg:col-span-8 space-y-8">
            
            @if(Auth::user()->role !== 'colaborador')
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-light text-gray-800 dark:text-white tracking-wide flex items-center gap-3">
                    Proyectos Activos
                </h2>
                @if(in_array(Auth::user()->role, ['admin', 'ceo']))
                <a href="{{ route('projects.create') }}" class="bg-gray-900 dark:bg-white text-white dark:text-black hover:bg-gray-800 dark:hover:bg-gray-200 transition-colors px-4 py-2 rounded-full font-medium text-xs uppercase tracking-widest">
                    Crear Nuevo
                </a>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse($projects as $project)
                    @php
                        $allSubtasks = $project->tasks->flatMap->subtasks;
                        $totalTasks = $allSubtasks->count();
                        $completedTasks = $allSubtasks->where('is_completed', true)->count();
                        $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
                        $pendingTasksCount = $totalTasks - $completedTasks;
                    @endphp
                    
                    <div class="group relative bg-white dark:bg-white/[0.03] backdrop-blur-md border border-gray-200 dark:border-white/10 hover:bg-gray-50 dark:hover:bg-white/[0.08] hover:border-gray-300 dark:hover:border-white/20 transition-all duration-500 rounded-2xl p-8 flex flex-col h-full shadow-sm hover:shadow-2xl dark:hover:shadow-black/50">
                        
                        @if(in_array(Auth::user()->role, ['admin', 'ceo']))
                        <div class="absolute top-5 right-5 z-30" x-data="{ open: false }">
                            <button @click="open = !open" @click.away="open = false" class="text-gray-600 hover:text-white transition-colors p-2 rounded-full hover:bg-white/5">
                                <i class="fas fa-ellipsis-v text-sm"></i>
                            </button>
                            
                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 translate-y-2"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 class="absolute right-0 mt-2 w-40 bg-[#1a1a1a] border border-white/10 rounded-xl shadow-xl overflow-hidden z-40"
                                 style="display: none;">
                                
                                <a href="{{ route('projects.edit', $project) }}" class="block px-4 py-3 text-xs font-medium text-gray-300 hover:bg-white/5 hover:text-white transition-colors">
                                    Editar Proyecto
                                </a>
                                
                                <form action="{{ route('projects.destroy', $project) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este proyecto? Esta acción no se puede deshacer.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="block w-full text-left px-4 py-3 text-xs font-medium text-red-400 hover:bg-red-500/10 hover:text-red-300 transition-colors">
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endif

                        <a href="{{ route('projects.show', $project) }}" class="absolute inset-0 z-10"></a>

                        <div class="flex justify-between items-start mb-8 relative z-0">
                            <div class="w-12 h-12 rounded-xl bg-black/20 border border-white/5 flex items-center justify-center text-white/80 text-lg group-hover:text-orange-500 transition-colors duration-500">
                                <i class="fas fa-cube"></i>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-1.5 h-1.5 rounded-full {{ $progress == 100 ? 'bg-green-500' : 'bg-orange-600' }}"></div>
                                <span class="text-[10px] font-medium uppercase tracking-widest text-gray-500">
                                    {{ $progress == 100 ? 'Completado' : 'En Curso' }}
                                </span>
                            </div>
                        </div>

                        <div class="mb-auto relative z-0">
                            <h3 class="text-xl font-medium text-gray-900 dark:text-white mb-2 group-hover:text-orange-500 transition-colors duration-300">{{ $project->name }}</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm font-light leading-relaxed line-clamp-2">{{ $project->description ?? 'Sin descripción.' }}</p>
                        </div>

                        <div class="mt-8 relative z-0">
                            <div class="flex justify-between text-[10px] font-medium text-gray-500 uppercase tracking-wider mb-3">
                                <span>Progreso</span>
                                <span>{{ $progress }}%</span>
                            </div>
                            <div class="w-full bg-white/5 h-[2px] overflow-hidden rounded-full">
                                <div class="h-full transition-all duration-1000 ease-out {{ $progress == 100 ? 'bg-green-500' : 'bg-orange-600' }}" style="width: {{ $progress }}%"></div>
                            </div>
                        </div>

                        <div class="flex justify-between items-center mt-6 pt-6 border-t border-white/5 relative z-0">
                            <div class="flex -space-x-2">
                                @foreach($team->take(3) as $member)
                                    <div class="w-7 h-7 rounded-full bg-[#141414] border border-black flex items-center justify-center text-[9px] text-white font-medium relative hover:z-10 hover:scale-110 transition-transform" title="{{ $member->name }}">
                                        @if($member->photo)
                                            <img src="{{ asset('storage/' . $member->photo) }}" class="w-full h-full rounded-full object-cover">
                                        @else
                                            <div class="w-full h-full rounded-full bg-gray-800 flex items-center justify-center text-gray-300">
                                                {{ substr($member->name, 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            <span class="text-[10px] font-medium text-gray-600 uppercase tracking-wider">
                                {{ $pendingTasksCount }} Tareas pendientes
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-2 py-16 text-center border border-dashed border-white/5 rounded-2xl">
                        <p class="text-gray-600 font-light mb-4">No hay proyectos activos.</p>
                    </div>
                @endforelse
            </div>
            @else
            <!-- VISTA ESPECIAL PARA COLABORADOR -->
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-light text-white tracking-wide">Mis Tareas Asignadas</h2>
            </div>

            <div class="space-y-6">
                @forelse($projects as $project)
                    @php
                        $user = Auth::user();
                        $teamMemberId = $user->teamMember ? $user->teamMember->id : null;
                        $mySubtasks = $project->tasks->flatMap->subtasks->where('team_member_id', $teamMemberId);
                        $totalTasks = $mySubtasks->count();
                        $completedTasks = $mySubtasks->where('is_completed', true)->count();
                        $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
                        $pendingTasks = $mySubtasks->where('is_completed', false);
                    @endphp
                    
                    <div class="bg-white dark:bg-white/[0.02] border border-gray-200 dark:border-white/5 rounded-3xl overflow-hidden group hover:border-orange-500/20 transition-all duration-500 shadow-sm dark:shadow-none">
                        <div class="p-6 border-b border-gray-100 dark:border-white/5 bg-gray-50/30 dark:bg-white/[0.01] flex justify-between items-center">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-orange-500/10 flex items-center justify-center text-orange-500">
                                    <i class="fas fa-layer-group text-sm"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ $project->name }}</h3>
                                    <div class="flex items-center gap-3 mt-1">
                                        <span class="text-[9px] font-bold text-gray-500 uppercase tracking-widest">{{ $totalTasks }} Tareas asignadas</span>
                                        <div class="w-1 h-1 rounded-full bg-gray-700"></div>
                                        <span class="text-[9px] font-bold text-orange-500 uppercase tracking-widest">{{ $progress }}% Completado</span>
                                    </div>
                                </div>
                            </div>
                            <a href="{{ route('projects.show', $project) }}" class="text-[10px] font-bold text-gray-500 hover:text-white uppercase tracking-widest px-4 py-2 rounded-lg border border-white/5 hover:bg-white/5 transition-all">Ver Proyecto completo</a>
                        </div>

                        <div class="p-6 space-y-3">
                            @forelse($pendingTasks as $task)
                                <div @click="$dispatch('open-task', { task: @js($task), sectionTitle: @js($task->task->title ?? 'General'), parentTitle: @js($task->parent->title ?? '') })" class="flex items-center gap-4 p-4 rounded-2xl bg-gray-50/50 dark:bg-white/[0.03] border border-gray-100 dark:border-white/5 hover:border-gray-200 dark:hover:border-white/10 hover:bg-gray-100 dark:hover:bg-white/[0.05] transition-all cursor-pointer group/task shadow-sm">
                                    <div class="w-5 h-5 rounded-lg border-2 border-orange-500/30 flex items-center justify-center group-hover/task:border-orange-500 transition-colors">
                                        <div class="w-2 h-2 rounded-full bg-orange-500 opacity-0 group-hover/task:opacity-100 transition-opacity"></div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] font-bold text-orange-500/80 uppercase tracking-tighter">{{ $task->task->title ?? 'General' }}</span>
                                            @if($task->parent)
                                                <i class="fas fa-chevron-right text-[7px] text-gray-600"></i>
                                                <span class="text-[10px] font-bold text-gray-500 uppercase tracking-tighter">{{ $task->parent->title }}</span>
                                            @endif
                                        </div>
                                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">{{ $task->title }}</p>
                                        <div class="flex items-center gap-4 mt-1">
                                            @if($task->due_date)
                                                <span class="text-[9px] font-bold uppercase {{ \Carbon\Carbon::parse($task->due_date)->isPast() ? 'text-red-500' : 'text-gray-500' }}">
                                                    <i class="far fa-calendar-alt mr-1"></i> {{ \Carbon\Carbon::parse($task->due_date)->format('d M, h:i A') }}
                                                </span>
                                            @endif
                                            @if($task->comments->count() > 0)
                                                <span class="text-[9px] font-bold text-gray-600 uppercase"><i class="far fa-comment-alt mr-1"></i> {{ $task->comments->count() }}</span>
                                            @endif
                                            @if($task->attachments->count() > 0)
                                                <span class="text-[9px] font-bold text-gray-600 uppercase"><i class="fas fa-paperclip mr-1"></i> {{ $task->attachments->count() }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <i class="fas fa-chevron-right text-[10px] text-gray-700 group-hover/task:text-gray-400 group-hover/task:translate-x-1 transition-all"></i>
                                </div>
                            @empty
                                <div class="py-4 text-center">
                                    <p class="text-[10px] text-gray-600 uppercase tracking-widest italic">No tienes tareas pendientes en este proyecto</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                @empty
                    <div class="col-span-2 py-16 text-center border border-dashed border-white/5 rounded-2xl">
                        <p class="text-gray-600 font-light mb-4">No hay tareas activas.</p>
                    </div>
                @endforelse
            </div>
            @endif
        </div>

        <!-- COLUMNA LATERAL -->
        <div class="lg:col-span-4 space-y-8">
            <div class="bg-white dark:bg-white/[0.03] backdrop-blur-md border border-gray-200 dark:border-white/10 rounded-2xl p-8 relative overflow-hidden shadow-sm">
                <div class="absolute top-0 right-0 w-32 h-32 bg-orange-500/5 rounded-full blur-3xl -mr-10 -mt-10"></div>
                <div class="relative z-10">
                    <i class="fas fa-quote-left text-orange-500/50 text-xl mb-4"></i>
                    <p class="font-light text-gray-600 dark:text-gray-300 italic leading-relaxed text-sm mb-4">"La simplicidad es la máxima sofisticación."</p>
                    <p class="text-[10px] font-bold text-gray-400 dark:text-gray-600 uppercase tracking-widest">— Leonardo da Vinci</p>
                </div>
            </div>

            <div class="bg-white dark:bg-white/[0.03] backdrop-blur-md border border-gray-200 dark:border-white/10 rounded-2xl p-8 shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-sm font-medium text-gray-800 dark:text-white uppercase tracking-widest">Agenda</h3>
                    <span class="text-[10px] text-gray-600">{{ date('M Y') }}</span>
                </div>
                <div class="space-y-1">
                    @php
                        $user = Auth::user();
                        $teamMemberId = $user->teamMember ? $user->teamMember->id : null;
                        $agendaTasks = $projects->flatMap->tasks->flatMap->subtasks
                            ->where('is_completed', false)
                            ->whereNotNull('due_date')
                            ->when($user->role === 'colaborador', function($collection) use ($teamMemberId) {
                                return $collection->where('team_member_id', $teamMemberId);
                            })
                            ->sortBy('due_date')
                            ->take(5);
                    @endphp
                    @forelse($agendaTasks as $task)
                        <div @click="$dispatch('open-task', { task: @js($task), sectionTitle: @js($task->task->title ?? 'General'), parentTitle: @js($task->parent->title ?? '') })" class="group flex items-center gap-4 p-3 rounded-xl hover:bg-white dark:hover:bg-white/5 transition-colors cursor-pointer border border-transparent hover:border-gray-200 dark:hover:border-white/5">
                            <div class="flex flex-col items-center justify-center w-10 h-10 rounded-lg bg-gray-100 dark:bg-black/20 border border-gray-200 dark:border-white/5 text-gray-500 dark:text-gray-400 group-hover:text-orange-500 transition-colors">
                                <span class="text-[10px] font-bold">{{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('d/m') : 'Hoy' }}</span>
                                <span class="text-[8px] opacity-50">{{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('h:i') : '' }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-light text-gray-700 dark:text-gray-300 truncate group-hover:text-gray-900 dark:group-hover:text-white transition-colors">{{ $task->title }}</h4>
                                <p class="text-[10px] text-gray-500 dark:text-gray-600 font-medium uppercase tracking-wider mt-1">{{ $task->project->name ?? 'General' }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 text-gray-600 text-xs font-light">Agenda libre.</div>
                    @endforelse
                </div>
            </div>

            <div class="bg-white dark:bg-white/[0.03] backdrop-blur-md border border-gray-200 dark:border-white/10 rounded-2xl p-8 shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-sm font-medium text-gray-800 dark:text-white uppercase tracking-widest">Equipo</h3>
                    @if(in_array(Auth::user()->role, ['admin', 'ceo', 'rrhh', 'contabilidad']))
                    <a href="{{ route('team.index') }}" class="text-[10px] font-bold text-gray-600 hover:text-white transition-colors uppercase tracking-widest">Ver Todo</a>
                    @endif
                </div>
                <div class="space-y-4">
                    @foreach($team->take(4) as $member)
                        <div class="flex items-center gap-4 group cursor-pointer">
                            @if($member->photo)
                                <img src="{{ asset('storage/' . $member->photo) }}" class="w-8 h-8 rounded-full object-cover border border-white/5 group-hover:border-orange-500/50 transition-colors">
                            @else
                                <div class="w-8 h-8 rounded-full bg-gray-800 border border-white/5 flex items-center justify-center text-white font-medium text-[10px] group-hover:border-orange-500/50 transition-colors">
                                    {{ substr($member->name, 0, 2) }}
                                </div>
                            @endif
                            <div class="flex-1">
                                <p class="text-sm font-light text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white transition-colors">{{ $member->name }}</p>
                                <p class="text-[10px] text-gray-500 dark:text-gray-600 uppercase tracking-wider">{{ $member->position ?? 'Staff' }}</p>
                            </div>
                            <div class="w-1.5 h-1.5 rounded-full bg-green-900 group-hover:bg-green-500 transition-colors"></div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
