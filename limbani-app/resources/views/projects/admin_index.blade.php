@extends('layouts.asana')

@section('content')
<div class="max-w-[1600px] mx-auto px-8 py-12">
    
    <!-- Header Minimalista -->
    <div class="mb-16 flex justify-between items-end border-b border-gray-200 dark:border-white/5 pb-8">
        <div>
            <h1 class="text-4xl font-light text-gray-800 dark:text-white tracking-wide mb-1">
                Gestión <span class="font-medium text-gray-900 dark:text-white">Administrativa</span>
            </h1>
            <p class="text-gray-500 text-xs font-medium tracking-[0.15em] uppercase">
                {{ strtoupper($category) }} — {{ \Carbon\Carbon::now()->locale('es')->isoFormat('dddd, D [de] MMMM, YYYY') }}
            </p>
        </div>
    </div>

    <!-- Pestañas de Categorías Administrativas -->
    <div class="flex items-center gap-8 border-b border-gray-200 dark:border-white/5 mb-10 text-sm font-medium">
        <a href="{{ route('admin-projects.index', ['category' => 'rrhh']) }}" class="pb-4 {{ $category === 'rrhh' ? 'text-gray-900 dark:text-white border-b-2 border-orange-500' : 'text-gray-500 hover:text-gray-300 transition-colors' }}">
            Recursos Humanos
        </a>
        <a href="{{ route('admin-projects.index', ['category' => 'administrativo']) }}" class="pb-4 {{ $category === 'administrativo' ? 'text-gray-900 dark:text-white border-b-2 border-orange-500' : 'text-gray-500 hover:text-gray-300 transition-colors' }}">
            Dirección Admin.
        </a>
        <a href="{{ route('admin-projects.index', ['category' => 'contabilidad']) }}" class="pb-4 {{ $category === 'contabilidad' ? 'text-gray-900 dark:text-white border-b-2 border-orange-500' : 'text-gray-500 hover:text-gray-300 transition-colors' }}">
            Contaduría
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
        
        <!-- COLUMNA PRINCIPAL -->
        <div class="lg:col-span-8 space-y-8">
            
            <div x-data="{ search: '' }">
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-8 gap-4">
                    <h2 class="text-xl font-light text-gray-800 dark:text-white tracking-wide">
                        Proyectos en {{ ucfirst($category) }}
                    </h2>
                    
                    <div class="flex flex-col sm:flex-row items-center gap-4 w-full md:w-auto">
                        <div class="relative w-full sm:w-64 group">
                            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-orange-500 transition-colors"></i>
                            <input type="text" x-model="search" placeholder="Buscar proyecto..." class="w-full bg-white/50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-full py-2 pl-10 pr-4 text-xs focus:ring-1 focus:ring-orange-500 outline-none transition-all dark:text-white">
                        </div>

                        @if(in_array(Auth::user()->role, ['admin', 'ceo', 'rrhh', 'contabilidad']))
                        <button onclick="document.getElementById('modal-create-admin').classList.remove('hidden')" class="w-full sm:w-auto bg-orange-600 text-white hover:bg-orange-700 transition-colors px-6 py-2.5 rounded-full font-medium text-[10px] uppercase tracking-widest shadow-lg">
                            Nuevo Proyecto
                        </button>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @forelse($projects as $project)
                        <div x-show="search === '' || '{{ strtolower($project->name) }}'.includes(search.toLowerCase())"
                             class="group relative bg-white/80 dark:bg-[#1a1a1a]/70 backdrop-blur-2xl border border-gray-200 dark:border-white/20 hover:border-orange-500/50 transition-all duration-500 rounded-2xl p-8 flex flex-col h-full shadow-sm hover:shadow-xl transform hover:-translate-y-1">
                            
                            <a href="{{ route('projects.show', $project) }}" class="absolute inset-0 z-10"></a>

                            <div class="flex justify-between items-start mb-6">
                                <div class="w-12 h-12 rounded-xl bg-orange-500/10 border border-orange-500/20 flex items-center justify-center text-orange-500 text-lg">
                                    <i class="fas fa-folder-open"></i>
                                </div>
                                <span class="text-[9px] font-bold uppercase tracking-widest text-gray-500 bg-gray-100 dark:bg-white/5 px-2 py-1 rounded-md">
                                    {{ $project->status }}
                                </span>
                            </div>

                            <div class="mb-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2 group-hover:text-orange-500 transition-colors">{{ $project->name }}</h3>
                                <p class="text-gray-600 dark:text-gray-400 text-xs font-light line-clamp-2">{{ $project->description ?? 'Sin descripción.' }}</p>
                            </div>

                            <div class="mt-auto flex justify-between items-center pt-6 border-t border-gray-100 dark:border-white/5 text-[10px] text-gray-500 font-medium uppercase tracking-wider">
                                <span>{{ $project->tasks->count() }} Secciones</span>
                                <span>Creado: {{ $project->created_at->format('d/m/Y') }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-2 py-16 text-center border border-dashed border-gray-200 dark:border-white/10 rounded-2xl">
                            <p class="text-gray-500 font-light italic">No hay proyectos registrados en esta categoría.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- COLUMNA LATERAL (Estadísticas simples o Ayuda) -->
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-gray-900 text-white rounded-2xl p-8 shadow-xl relative overflow-hidden">
                <div class="relative z-10">
                    <h3 class="text-sm font-bold uppercase tracking-[0.2em] mb-6 text-orange-500">Resumen {{ ucfirst($category) }}</h3>
                    <div class="space-y-6">
                        <div class="flex justify-between items-center border-b border-white/10 pb-4">
                            <span class="text-xs font-light text-gray-400">Total Proyectos</span>
                            <span class="text-xl font-medium">{{ $projects->count() }}</span>
                        </div>
                        <div class="flex justify-between items-center border-b border-white/10 pb-4">
                            <span class="text-xs font-light text-gray-400">Tareas Activas</span>
                            <span class="text-xl font-medium">{{ $projects->flatMap->tasks->flatMap->subtasks->where('is_completed', false)->count() }}</span>
                        </div>
                    </div>
                </div>
                <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-orange-500/10 rounded-full blur-3xl"></div>
            </div>

            <!-- Botonera de ayuda o accesos rápidos -->
            <div class="bg-white dark:bg-white/[0.03] border border-gray-200 dark:border-white/10 rounded-2xl p-6">
                <h4 class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-4">Recursos</h4>
                <div class="grid grid-cols-1 gap-2">
                    <a href="#" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-white/5 transition-colors text-xs text-gray-600 dark:text-gray-400">
                        <i class="fas fa-file-invoice-dollar text-orange-500"></i>
                        Manual de Procesos
                    </a>
                    <a href="#" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-white/5 transition-colors text-xs text-gray-600 dark:text-gray-400">
                        <i class="fas fa-user-shield text-orange-500"></i>
                        Políticas de la Empresa
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Simple para crear proyecto Administrativo -->
<div id="modal-create-admin" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="this.parentElement.classList.add('hidden')"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md p-8 bg-white dark:bg-[#1a1a1a] rounded-3xl shadow-2xl border border-white/10">
        <h2 class="text-2xl font-light text-gray-900 dark:text-white mb-6">Nuevo Proyecto <span class="font-medium text-orange-500">{{ ucfirst($category) }}</span></h2>
        <form action="{{ route('admin-projects.store') }}" method="POST">
            @csrf
            <input type="hidden" name="category" value="{{ $category }}">
            <div class="space-y-4">
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-2">Nombre del Proyecto</label>
                    <input type="text" name="name" required class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-3 text-sm focus:ring-1 focus:ring-orange-500 outline-none dark:text-white" placeholder="Ej: Auditoría Trimestral">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-2">Descripción</label>
                    <textarea name="description" rows="3" class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-3 text-sm focus:ring-1 focus:ring-orange-500 outline-none dark:text-white" placeholder="Breve descripción del alcance..."></textarea>
                </div>
            </div>
            <div class="mt-8 flex gap-3">
                <button type="button" onclick="document.getElementById('modal-create-admin').classList.add('hidden')" class="flex-1 px-6 py-3 rounded-xl border border-gray-200 dark:border-white/10 text-xs font-bold text-gray-500 uppercase tracking-widest hover:bg-gray-50 dark:hover:bg-white/5 transition-all">Cancelar</button>
                <button type="submit" class="flex-1 px-6 py-3 rounded-xl bg-orange-600 text-white text-xs font-bold uppercase tracking-widest hover:bg-orange-700 shadow-lg shadow-orange-900/40 transition-all">Crear Proyecto</button>
            </div>
        </form>
    </div>
</div>
@endsection
