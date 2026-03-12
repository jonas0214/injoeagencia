@extends('layouts.asana')

@section('content')
<style>
    @media (min-width: 768px) { aside { display: none !important; } main { margin-left: 0 !important; width: 100% !important; max-width: 100% !important; } }
</style>

<div class="flex h-full w-full bg-white dark:bg-[#0f1012] text-gray-800 dark:text-white overflow-hidden">
    <div class="flex-1 flex flex-col min-w-0">
        
        <!-- Header -->
        <div class="px-4 md:px-8 py-4 md:py-6 border-b border-gray-200 dark:border-white/5 bg-white dark:bg-[#0f1012] z-10 flex justify-between items-center shrink-0">
            <div class="flex items-center gap-4">
                <a href="{{ route('projects.show', $project) }}" class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-[#1a1a1a] border border-gray-200 dark:border-white/10 flex items-center justify-center shadow-sm hover:bg-gray-200 dark:hover:bg-white/5 transition-colors text-gray-400">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-xl md:text-2xl font-medium tracking-tight text-gray-900 dark:text-white">Reporte de Brief Estratégico</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $project->name }} - {{ $brief->updated_at->format('F Y') }}</p>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <div class="px-4 py-2 rounded-full text-[10px] font-bold uppercase tracking-[0.2em] 
                    @if($brief->status === 'draft') bg-gray-500/10 text-gray-500
                    @elseif($brief->status === 'submitted') bg-blue-500/10 text-blue-500
                    @elseif($brief->status === 'reviewed') bg-yellow-500/10 text-yellow-500
                    @elseif($brief->status === 'approved') bg-green-500/10 text-green-500
                    @endif">
                    {{ $brief->status === 'draft' ? 'Borrador' : 
                       ($brief->status === 'submitted' ? 'Enviado' : 
                       ($brief->status === 'reviewed' ? 'Revisado' : 'Aprobado')) }}
                </div>
                <a href="{{ route('briefs.edit', $project) }}" class="bg-orange-500 hover:bg-orange-600 text-black px-5 py-2 rounded-xl text-xs font-bold uppercase tracking-widest transition-all">
                    <i class="fas fa-edit mr-2"></i> Editar
                </a>
            </div>
        </div>

        <!-- Contenido del Reporte -->
        <div class="flex-1 overflow-y-auto custom-scroll p-4 md:p-12">
            <div class="max-w-4xl mx-auto space-y-12 pb-20">
                
                @php
                    $sections = [
                        ['title' => 'Prioridades del Mes', 'icon' => 'fas fa-bullseye', 'color' => 'orange', 'qs' => [
                            'q1' => 'Lanzamientos, promociones o novedades',
                            'q2' => 'Producto/Servicio con mayor visibilidad',
                            'q3' => 'Objetivo comercial principal'
                        ]],
                        ['title' => 'Mensaje Estratégico', 'icon' => 'fas fa-comment-dots', 'color' => 'blue', 'qs' => [
                            'q4' => 'Mensaje principal a comunicar',
                            'q5' => 'Campañas internas o anuncios'
                        ]],
                        ['title' => 'Productos a Destacar', 'icon' => 'fas fa-gem', 'color' => 'purple', 'qs' => [
                            'q6' => 'Productos/servicios a promocionar',
                            'q7' => 'Prioridad máxima de venta',
                            'q8' => 'Promociones o descuentos'
                        ]],
                        ['title' => 'Fechas y Oportunidades', 'icon' => 'fas fa-calendar-star', 'color' => 'yellow', 'qs' => [
                            'q9' => 'Fechas especiales o eventos',
                            'q10' => 'Casos de éxito o experiencias'
                        ]],
                        ['title' => 'Contenido Estratégico', 'icon' => 'fas fa-paint-brush', 'color' => 'indigo', 'qs' => [
                            'q11' => 'Tipo de contenido priorizado',
                            'q12' => 'Preguntas frecuentes a responder',
                            'q13' => 'Temas a EVITAR'
                        ]],
                        ['title' => 'Recursos Disponibles', 'icon' => 'fas fa-camera', 'color' => 'green', 'qs' => [
                            'q14' => 'Material gráfico/video disponible',
                            'q15' => 'Personas que pueden participar'
                        ]],
                        ['title' => 'Publicidad (Meta Ads)', 'icon' => 'fas fa-rocket', 'color' => 'red', 'qs' => [
                            'q16' => '¿Realizar pauta publicitaria?',
                            'q17' => 'Presupuesto total de pauta',
                            'q18' => 'Distribución del presupuesto'
                        ]],
                        ['title' => 'Resultados Esperados', 'icon' => 'fas fa-trophy', 'color' => 'amber', 'qs' => [
                            'q19' => 'Resultado ideal tras la estrategia'
                        ]],
                        ['title' => 'Información Adicional', 'icon' => 'fas fa-info-circle', 'color' => 'gray', 'qs' => [
                            'q20' => 'Observaciones finales'
                        ]],
                    ];
                @endphp

                @if(is_array($brief->answers))
                    @foreach($sections as $section)
                        <section class="space-y-6">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-{{ $section['color'] }}-500/10 flex items-center justify-center text-{{ $section['color'] }}-500 text-xl border border-{{ $section['color'] }}-500/20">
                                    <i class="{{ $section['icon'] }}"></i>
                                </div>
                                <h2 class="text-2xl font-bold tracking-tight">{{ $section['title'] }}</h2>
                            </div>

                            <div class="grid grid-cols-1 gap-4">
                                @foreach($section['qs'] as $key => $label)
                                    <div class="p-6 rounded-3xl bg-gray-50 dark:bg-white/[0.02] border border-gray-200 dark:border-white/10">
                                        <h4 class="text-[10px] font-bold uppercase tracking-[0.2em] text-gray-400 mb-3">{{ $label }}</h4>
                                        <div class="text-gray-900 dark:text-gray-200 leading-relaxed font-medium">
                                            @php $val = $brief->answers[$key] ?? null; @endphp
                                            @if(is_array($val))
                                                <div class="flex flex-wrap gap-2">
                                                    @foreach($val as $item)
                                                        <span class="px-3 py-1 bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-lg text-xs font-bold">{{ $item }}</span>
                                                    @endforeach
                                                </div>
                                            @elseif($val)
                                                {!! nl2br(e($val)) !!}
                                            @else
                                                <span class="text-gray-400 italic text-sm">Sin respuesta</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endforeach
                @else
                    <div class="text-center py-20 bg-gray-50 dark:bg-white/[0.02] rounded-[3rem] border border-dashed border-gray-200 dark:border-white/10">
                        <i class="fas fa-exclamation-triangle text-orange-500 text-4xl mb-4"></i>
                        <h3 class="text-xl font-bold">Datos de Brief no encontrados</h3>
                        <p class="text-gray-500 mt-2">Este brief fue creado en una versión anterior de Limbani. Por favor, edítalo para actualizarlo al nuevo formato estratégico.</p>
                        <a href="{{ route('briefs.edit', $project) }}" class="mt-6 inline-block bg-orange-500 text-black px-8 py-3 rounded-2xl font-bold uppercase tracking-widest text-xs">Actualizar Formato</a>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection
