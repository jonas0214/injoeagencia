@extends('layouts.asana')

@section('content')
<style>
    body {
        background: linear-gradient(to bottom, #111827, #000000) !important;
        min-height: 100vh;
    }
</style>
<div class="max-w-[1600px] mx-auto px-8 py-12">
    
    <!-- Header Minimalista -->
    <div class="mb-16 flex justify-between items-end border-b border-white/5 pb-8">
        <div>
            <h1 class="text-4xl font-light text-white tracking-wide mb-1">
                Bienvenido, <span class="font-medium text-white">{{ Auth::user()->name }}</span>
            </h1>
            <p class="text-gray-500 text-xs font-medium tracking-[0.15em] uppercase">
                {{ \Carbon\Carbon::now()->locale('es')->isoFormat('dddd, D [de] MMMM, YYYY') }}
            </p>
        </div>
        <a href="{{ route('projects.create') }}" class="bg-white text-black hover:bg-gray-200 transition-colors px-6 py-2.5 rounded-full font-medium text-xs uppercase tracking-widest">
            <i class="fas fa-plus mr-2 text-[10px]"></i> Quick Action
        </a>
    </div>

    <!-- Pestañas de Navegación Estilo Asana -->
    <div class="flex items-center gap-8 border-b border-white/5 mb-10 text-sm font-medium">
        <a href="{{ route('dashboard') }}" class="pb-4 {{ request()->routeIs('dashboard') ? 'text-white border-b-2 border-orange-500' : 'text-gray-500 hover:text-gray-300 transition-colors' }}">
            Proyectos
        </a>
        <a href="{{ route('team.index') }}" class="pb-4 {{ request()->routeIs('team.index') ? 'text-white border-b-2 border-orange-500' : 'text-gray-500 hover:text-gray-300 transition-colors' }}">
            Equipo & Nómina
        </a>
        <a href="#" class="pb-4 text-gray-500 hover:text-gray-300 transition-colors opacity-50 cursor-not-allowed">
            Informes
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
        
        <!-- COLUMNA PRINCIPAL: PROYECTOS (col-span-8) -->
        <div class="lg:col-span-8 space-y-8">
            
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-light text-white tracking-wide flex items-center gap-3">
                    Proyectos Activos
                </h2>
                <a href="{{ route('projects.create') }}" class="bg-white text-black hover:bg-gray-200 transition-colors px-4 py-2 rounded-full font-medium text-xs uppercase tracking-widest">
                    Crear Nuevo
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse($projects as $project)
                    @php
                        // Contamos las subtareas de las tareas (secciones) del proyecto
                        $allSubtasks = $project->tasks->flatMap->subtasks;
                        $totalTasks = $allSubtasks->count();
                        $completedTasks = $allSubtasks->where('is_completed', true)->count();
                        $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
                        $pendingTasks = $totalTasks - $completedTasks;
                    @endphp
                    
                    <!-- Tarjeta Agency Elite -->
                    <!-- Usamos 'group' para efectos hover y 'relative' para posicionamiento -->
                    <div class="group relative bg-white/[0.03] backdrop-blur-md border border-white/10 hover:bg-white/[0.08] hover:border-white/20 transition-all duration-500 rounded-2xl p-8 flex flex-col h-full hover:shadow-2xl hover:shadow-black/50">
                        
                        <!-- Menú de Administración (Alpine.js) -->
                        <div class="absolute top-5 right-5 z-30" x-data="{ open: false }">
                            <button @click="open = !open" @click.away="open = false" class="text-gray-600 hover:text-white transition-colors p-2 rounded-full hover:bg-white/5">
                                <i class="fas fa-ellipsis-v text-sm"></i>
                            </button>
                            
                            <!-- Dropdown -->
                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 translate-y-2"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 translate-y-2"
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

                        <!-- Enlace Principal (Stretched Link) -->
                        <!-- Cubre toda la tarjeta excepto el menú superior -->
                        <a href="{{ route('projects.show', $project) }}" class="absolute inset-0 z-10"></a>

                        <!-- Cabecera Tarjeta -->
                        <div class="flex justify-between items-start mb-8 relative z-0">
                            <div class="w-12 h-12 rounded-xl bg-black/20 border border-white/5 flex items-center justify-center text-white/80 text-lg group-hover:text-orange-500 transition-colors duration-500">
                                <i class="fas fa-cube"></i>
                            </div>
                            <!-- Badge Minimalista -->
                            <div class="flex items-center gap-2">
                                <div class="w-1.5 h-1.5 rounded-full {{ $progress == 100 ? 'bg-green-500' : 'bg-orange-600' }}"></div>
                                <span class="text-[10px] font-medium uppercase tracking-widest text-gray-500">
                                    {{ $progress == 100 ? 'Completado' : 'En Curso' }}
                                </span>
                            </div>
                        </div>

                        <!-- Título y Descripción -->
                        <div class="mb-auto relative z-0">
                            <h3 class="text-xl font-medium text-white mb-2 group-hover:text-orange-500 transition-colors duration-300">{{ $project->name }}</h3>
                            <p class="text-gray-400 text-sm font-light leading-relaxed line-clamp-2">{{ $project->description ?? 'Sin descripción.' }}</p>
                        </div>

                        <!-- Barra de Progreso Sutil -->
                        <div class="mt-8 relative z-0">
                            <div class="flex justify-between text-[10px] font-medium text-gray-500 uppercase tracking-wider mb-3">
                                <span>Progreso</span>
                                <span>{{ $progress }}%</span>
                            </div>
                            <div class="w-full bg-white/5 h-[2px] overflow-hidden rounded-full">
                                <div class="h-full transition-all duration-1000 ease-out {{ $progress == 100 ? 'bg-green-500' : 'bg-orange-600' }}" style="width: {{ $progress }}%"></div>
                            </div>
                        </div>

                        <!-- Footer Tarjeta -->
                        <div class="flex justify-between items-center mt-6 pt-6 border-t border-white/5 relative z-0">
                            <div class="flex -space-x-2">
                                @foreach($team->take(3) as $member)
                                    <div class="w-7 h-7 rounded-full bg-[#141414] border border-black flex items-center justify-center text-[9px] text-white font-medium relative hover:z-10 hover:scale-110 transition-transform" title="{{ $member->name }}">
                                        <div class="w-full h-full rounded-full bg-gray-800 flex items-center justify-center text-gray-300">
                                            {{ substr($member->name, 0, 1) }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <span class="text-[10px] font-medium text-gray-600 uppercase tracking-wider">
                                {{ $pendingTasks }} Tareas pendientes
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-2 py-16 text-center border border-dashed border-white/5 rounded-2xl">
                        <p class="text-gray-600 font-light mb-4">No hay proyectos activos.</p>
                        <a href="{{ route('projects.create') }}" class="text-orange-500 text-xs font-bold uppercase tracking-widest hover:text-white transition-colors">Iniciar Proyecto</a>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- COLUMNA LATERAL (col-span-4) -->
        <div class="lg:col-span-4 space-y-8">
            
            <!-- Widget Inspiración (Rediseñado Elegante) -->
            <div class="bg-white/[0.03] backdrop-blur-md border border-white/10 rounded-2xl p-8 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-orange-500/5 rounded-full blur-3xl -mr-10 -mt-10"></div>
                <div class="relative z-10">
                    <i class="fas fa-quote-left text-orange-500/50 text-xl mb-4"></i>
                    <p class="font-light text-gray-300 italic leading-relaxed text-sm mb-4">
                        "La simplicidad es la máxima sofisticación."
                    </p>
                    <p class="text-[10px] font-bold text-gray-600 uppercase tracking-widest">— Leonardo da Vinci</p>
                </div>
            </div>

            <!-- Widget Agenda (Minimalista) -->
            <div class="bg-white/[0.03] backdrop-blur-md border border-white/10 rounded-2xl p-8">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-sm font-medium text-white uppercase tracking-widest">Agenda</h3>
                    <span class="text-[10px] text-gray-600">{{ date('M Y') }}</span>
                </div>

                <div class="space-y-1">
                    @php
                        // Obtenemos las subtareas pendientes de todos los proyectos
                        $agendaTasks = $projects->flatMap->tasks->flatMap->subtasks
                            ->where('is_completed', false)
                            ->whereNotNull('due_date')
                            ->sortBy('due_date')
                            ->take(5);
                    @endphp

                    @forelse($agendaTasks as $task)
                        <div class="group flex items-center gap-4 p-3 rounded-xl hover:bg-white/5 transition-colors cursor-pointer border border-transparent hover:border-white/5">
                            <div class="flex flex-col items-center justify-center w-10 h-10 rounded-lg bg-black/20 border border-white/5 text-gray-400 group-hover:text-orange-500 transition-colors">
                                <span class="text-xs font-bold">{{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('d') : 'Hoy' }}</span>
                            </div>
                            
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-light text-gray-300 truncate group-hover:text-white transition-colors">{{ $task->title }}</h4>
                                <p class="text-[10px] text-gray-600 font-medium uppercase tracking-wider mt-1">
                                    {{ $task->project->name ?? 'General' }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 text-gray-600 text-xs font-light">
                            Agenda libre.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Widget Equipo (Lista Limpia) -->
            <div class="bg-white/[0.03] backdrop-blur-md border border-white/10 rounded-2xl p-8">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-sm font-medium text-white uppercase tracking-widest">Equipo</h3>
                    <a href="{{ route('team.index') }}" class="text-[10px] font-bold text-gray-600 hover:text-white transition-colors uppercase tracking-widest">Ver Todo</a>
                </div>
                
                <div class="space-y-4">
                    @foreach($team->take(4) as $member)
                        <div class="flex items-center gap-4 group cursor-pointer">
                            <div class="w-8 h-8 rounded-full bg-gray-800 border border-white/5 flex items-center justify-center text-white font-medium text-[10px] group-hover:border-orange-500/50 transition-colors">
                                {{ substr($member->name, 0, 2) }}
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-light text-gray-300 group-hover:text-white transition-colors">{{ $member->name }}</p>
                                <p class="text-[10px] text-gray-600 uppercase tracking-wider">{{ $member->position ?? 'Staff' }}</p>
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
