<!-- NAVEGACIÓN TIPO PESTAÑAS -->
<div class="flex items-center gap-4 md:gap-8 border-b border-white/5 mb-10 text-xs md:text-sm font-medium overflow-x-auto scrollbar-hide whitespace-nowrap">
    <a href="{{ route('dashboard') }}" class="pb-4 text-white border-b-2 border-orange-500">
        Proyectos
    </a>
    <a href="{{ route('team.index') }}" class="pb-4 text-gray-500 hover:text-gray-300 transition-colors">
        Equipo & Nómina
    </a>
    <a href="#" class="pb-4 text-gray-500 hover:text-gray-300 transition-colors opacity-50 cursor-not-allowed">
        Informes
    </a>
</div>

<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-12 gap-4">
    <div>
        <h3 class="text-xl md:text-2xl font-light text-white tracking-wide">Proyectos Activos</h3>
        <p class="text-[10px] text-gray-500 uppercase tracking-widest mt-1">Supervisa el progreso de tus campañas en tiempo real</p>
    </div>
    <a href="{{ route('projects.create') }}" class="w-full md:w-auto bg-[#1a1a1a] hover:bg-orange-600 text-white font-bold py-3 px-6 rounded-full shadow-lg transition flex items-center justify-center gap-2 text-xs uppercase tracking-widest border border-white/10">
        <i class="fas fa-plus"></i> Nuevo Proyecto
    </a>
</div>

@if($projects->isEmpty())
    <div class="text-center py-10">
        <p class="text-gray-500 italic">Aún no tienes campañas registradas en la agencia.</p>
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($projects as $project)
            <div class="group relative bg-[#1a1a1a] border border-white/5 rounded-[2rem] p-6 hover:border-orange-500/30 transition-all duration-500 hover:shadow-2xl hover:shadow-orange-500/10 overflow-hidden">
                <!-- Decoración -->
                <div class="absolute top-0 right-0 w-24 h-24 bg-orange-500/5 rounded-full blur-3xl group-hover:bg-orange-500/10 transition-colors"></div>

                <div class="flex justify-between items-start mb-6">
                    <div class="w-12 h-12 rounded-2xl bg-orange-500/10 flex items-center justify-center text-orange-500">
                        <i class="fas fa-layer-group text-xl"></i>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                        <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">{{ $project->status ?? 'En curso' }}</span>
                    </div>
                </div>

                <h4 class="text-xl font-bold text-white mb-2 group-hover:text-orange-500 transition-colors">
                    <a href="{{ route('projects.show', $project) }}" class="after:absolute after:inset-0">{{ $project->name }}</a>
                </h4>
                
                <p class="text-xs text-gray-500 leading-relaxed mb-6 line-clamp-2">
                    {{ $project->description ?? 'Este proyecto no tiene una descripción detallada asignada todavía.' }}
                </p>

                @php
                    $completed = $project->tasks->flatMap->subtasks->where('is_completed', true)->count();
                    $total = $project->tasks->flatMap->subtasks->count();
                    $percent = $total > 0 ? round(($completed / $total) * 100) : 0;
                @endphp

                <!-- Barra de Progreso -->
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between text-[10px] font-bold uppercase tracking-widest">
                        <span class="text-gray-600">Progreso</span>
                        <span class="text-white">{{ $percent }}%</span>
                    </div>
                    <div class="h-1.5 w-full bg-white/5 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-orange-600 to-orange-400 transition-all duration-1000" style="width: {{ $percent }}%"></div>
                    </div>
                </div>

                <div class="flex justify-between items-center pt-6 border-t border-white/5">
                    <div class="flex -space-x-2">
                        @foreach($project->tasks->flatMap->subtasks->pluck('user')->unique('id')->take(3) as $user)
                            <div class="w-7 h-7 rounded-full bg-[#111] border-2 border-[#1a1a1a] flex items-center justify-center text-[10px] text-gray-400 font-bold uppercase">
                                {{ substr($user->name ?? 'U', 0, 1) }}
                            </div>
                        @endforeach
                    </div>
                    <span class="text-[9px] font-bold text-gray-600 uppercase tracking-widest">
                        {{ $total }} Tareas
                    </span>
                </div>
            </div>
        @endforeach
    </div>
@endif