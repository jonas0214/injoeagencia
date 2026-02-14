<!-- NAVEGACIÓN TIPO PESTAÑAS (AGREGADO) -->
<div class="flex space-x-8 border-b border-gray-200 mb-8">
    <a href="{{ route('dashboard') }}" class="border-b-2 border-orange-500 py-4 px-1 text-sm font-bold text-orange-600">
        Campañas Activas
    </a>
    <a href="{{ route('team.index') }}" class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700 transition-colors">
        Equipo & Nómina
    </a>
</div>

<div class="flex justify-between items-center mb-6">
    <h3 class="text-lg font-bold text-gray-800">Campañas Activas</h3>
    <a href="{{ route('projects.create') }}" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded-md shadow-sm transition">
        + Nueva Campaña
    </a>
</div>

@if($projects->isEmpty())
    <div class="text-center py-10">
        <p class="text-gray-500 italic">Aún no tienes campañas registradas en la agencia.</p>
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($projects as $project)
            <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg hover:shadow-md transition">
                <h4 class="font-bold text-orange-600 text-lg">
                    <a href="{{ route('projects.show', $project) }}">{{ $project->name }}</a>
                </h4>
                <p class="text-sm text-gray-600 mb-2">{{ $project->description ?? 'Sin descripción' }}</p>
                <div class="flex justify-between items-center">
                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded uppercase">
                        {{ $project->status }}
                    </span>
                    <span class="text-xs text-gray-400">Creado: {{ $project->created_at->format('d/m/Y') }}</span>
                </div>
            </div>
        @endforeach
    </div>
@endif