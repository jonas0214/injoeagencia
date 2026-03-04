@extends('layouts.asana')

@section('content')
<style>
    @media (min-width: 768px) { aside { display: none !important; } main { margin-left: 0 !important; width: 100% !important; max-width: 100% !important; } }
    .dark body { background-color: #0f1012 !important; }
    body:not(.dark) { background-color: #f3f4f6 !important; }
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
                    <h1 class="text-xl md:text-2xl font-medium tracking-tight text-gray-900 dark:text-white">Brief del Cliente</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $project->name }}</p>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <div class="px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest 
                    @if($brief->status === 'draft') bg-gray-500/10 text-gray-500
                    @elseif($brief->status === 'submitted') bg-blue-500/10 text-blue-500
                    @elseif($brief->status === 'reviewed') bg-yellow-500/10 text-yellow-500
                    @elseif($brief->status === 'approved') bg-green-500/10 text-green-500
                    @endif">
                    {{ $brief->status === 'draft' ? 'Borrador' : 
                       ($brief->status === 'submitted' ? 'Enviado' : 
                       ($brief->status === 'reviewed' ? 'Revisado' : 'Aprobado')) }}
                </div>
            </div>
        </div>

        <!-- Formulario -->
        <div class="flex-1 overflow-y-auto custom-scroll p-4 md:p-8">
            <form id="briefForm" action="{{ route('briefs.update', $project) }}" method="POST" class="max-w-4xl mx-auto space-y-8">
                @csrf
                @method('PUT')
                
                <!-- Sección 1: Objetivos y Metas -->
                <div class="bg-white dark:bg-white/[0.02] border border-gray-200 dark:border-white/10 rounded-2xl p-6 md:p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-orange-500/10 flex items-center justify-center text-orange-500">
                            <i class="fas fa-bullseye"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Objetivos y Metas</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">¿Qué quieres alcanzar este mes?</p>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Objetivos principales del mes
                            </label>
                            <textarea name="objectives" rows="4" 
                                class="w-full bg-gray-50 dark:bg-[#111] border border-gray-200 dark:border-white/10 rounded-xl p-4 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent outline-none transition-all"
                                placeholder="Ejemplo: Aumentar ventas en un 20%, Lanzar nuevo producto, Mejorar engagement en redes sociales...">{{ old('objectives', $brief->objectives) }}</textarea>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Describe los objetivos específicos y medibles que quieres alcanzar.</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Métricas de éxito
                            </label>
                            <textarea name="success_metrics" rows="3" 
                                class="w-full bg-gray-50 dark:bg-[#111] border border-gray-200 dark:border-white/10 rounded-xl p-4 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent outline-none transition-all"
                                placeholder="Ejemplo: 1000 nuevos seguidores, 50 leads calificados, 10% tasa de conversión...">{{ old('success_metrics', $brief->success_metrics) }}</textarea>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">¿Cómo mediremos el éxito de esta campaña?</p>
                        </div>
                    </div>
                </div>

                <!-- Sección 2: Audiencia y Fechas -->
                <div class="bg-white dark:bg-white/[0.02] border border-gray-200 dark:border-white/10 rounded-2xl p-6 md:p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-500">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Audiencia y Fechas Clave</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">¿A quién nos dirigimos y cuándo?</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Audiencia objetivo
                            </label>
                            <textarea name="target_audience" rows="4" 
                                class="w-full bg-gray-50 dark:bg-[#111] border border-gray-200 dark:border-white/10 rounded-xl p-4 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent outline-none transition-all"
                                placeholder="Ejemplo: Mujeres 25-45 años, profesionales, interesadas en bienestar, residentes en Bogotá...">{{ old('target_audience', $brief->target_audience) }}</textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Fechas especiales importantes
                            </label>
                            <textarea name="key_dates" rows="4" 
                                class="w-full bg-gray-50 dark:bg-[#111] border border-gray-200 dark:border-white/10 rounded-xl p-4 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent outline-none transition-all"
                                placeholder="Ejemplo: 15 Marzo - Lanzamiento producto, 20 Marzo - Evento especial, 31 Marzo - Cierre de mes...">{{ old('key_dates', $brief->key_dates) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Sección 3: Presupuesto y Requerimientos -->
                <div class="bg-white dark:bg-white/[0.02] border border-gray-200 dark:border-white/10 rounded-2xl p-6 md:p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-green-500/10 flex items-center justify-center text-green-500">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Presupuesto y Requerimientos</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Recursos y especificaciones</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Presupuesto asignado
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500">$</span>
                                <input type="number" name="budget" step="0.01" min="0"
                                    value="{{ old('budget', $brief->budget) }}"
                                    class="w-full bg-gray-50 dark:bg-[#111] border border-gray-200 dark:border-white/10 rounded-xl pl-10 pr-4 py-3 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent outline-none transition-all"
                                    placeholder="0.00">
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Presupuesto total para esta campaña/mes</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Requerimientos especiales
                            </label>
                            <textarea name="special_requirements" rows="3" 
                                class="w-full bg-gray-50 dark:bg-[#111] border border-gray-200 dark:border-white/10 rounded-xl p-4 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent outline-none transition-all"
                                placeholder="Ejemplo: Necesitamos fotografía profesional, Videos cortos para TikTok, Traducción al inglés...">{{ old('special_requirements', $brief->special_requirements) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Sección 4: Mensajes y Contenido -->
                <div class="bg-white dark:bg-white/[0.02] border border-gray-200 dark:border-white/10 rounded-2xl p-6 md:p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-purple-500/10 flex items-center justify-center text-purple-500">
                            <i class="fas fa-comment-alt"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Mensajes y Contenido</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">¿Qué queremos comunicar?</p>
                        </div>
                    </div>
                    
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Mensajes clave a comunicar
                            </label>
                            <textarea name="key_messages" rows="3" 
                                class="w-full bg-gray-50 dark:bg-[#111] border border-gray-200 dark:border-white/10 rounded-xl p-4 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent outline-none transition-all"
                                placeholder="Ejemplo: Enfoque en calidad premium, Sostenibilidad como valor principal, Innovación constante...">{{ old('key_messages', $brief->key_messages) }}</textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Preferencias de contenido
                            </label>
                            <textarea name="content_preferences" rows="3" 
                                class="w-full bg-gray-50 dark:bg-[#111] border border-gray-200 dark:border-white/10 rounded-xl p-4 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent outline-none transition-all"
                                placeholder="Ejemplo: Contenido educativo, Testimonios de clientes, Demostraciones en video, Infografías...">{{ old('content_preferences', $brief->content_preferences) }}</textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Guías de marca (colores, tono, estilo)
                            </label>
                            <textarea name="brand_guidelines" rows="3" 
                                class="w-full bg-gray-50 dark:bg-[#111] border border-gray-200 dark:border-white/10 rounded-xl p-4 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent outline-none transition-all"
                                placeholder="Ejemplo: Colores corporativos #FF6B35 y #2D3047, Tono formal pero cercano, Usar logo en esquina superior derecha...">{{ old('brand_guidelines', $brief->brand_guidelines) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Sección 5: Análisis Competitivo -->
                <div class="bg-white dark:bg-white/[0.02] border border-gray-200 dark:border-white/10 rounded-2xl p-6 md:p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-red-500/10 flex items-center justify-center text-red-500">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Análisis Competitivo</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">¿Qué hacen los competidores?</p>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Análisis de competencia y diferenciadores
                        </label>
                        <textarea name="competitor_analysis" rows="4" 
                            class="w-full bg-gray-50 dark:bg-[#111] border border-gray-200 dark:border-white/10 rounded-xl p-4 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent outline-none transition-all"
                            placeholder="Ejemplo: Competidor X se enfoca en precio bajo, nosotros en calidad. Competidor Y tiene buena presencia en Instagram pero no en LinkedIn...">{{ old('competitor_analysis', $brief->competitor_analysis) }}</textarea>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Menciona competidores principales y cómo nos diferenciamos.</p>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="flex flex-col md:flex-row justify-between items-center gap-4 pt-8 border-t border-gray-200 dark:border-white/10">
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        <i class="fas fa-info-circle mr-2"></i>
                        Guarda como borrador o envía para revisión
                    </div>
                    
                    <div class="flex flex-wrap gap-3">
                        <button type="button" onclick="saveDraft()" 
                            class="px-6 py-3 bg-gray-900 dark:bg-white text-white dark:text-black hover:bg-gray-800 dark:hover:bg-gray-200 rounded-xl text-xs font-bold uppercase tracking-widest transition-colors shadow-lg">
                            <i class="fas fa-save mr-2"></i> Guardar Borrador
                        </button>
                        
                        @if($brief->status === 'draft')
                        <button type="button" onclick="submitBrief()" 
                            class="px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-xl text-xs font-bold uppercase tracking-widest transition-colors shadow-lg shadow-blue-500/20">
                            <i class="fas fa-paper-plane mr-2"></i> Enviar para Revisión
                        </button>
                        @endif
                        
                        @if(in_array(Auth::user()->role, ['admin', 'ceo']) && $brief->status === 'submitted')
                        <button type="button" onclick="reviewBrief()" 
                            class="px-6 py-3 bg-yellow-500 hover:bg-yellow-600 text-white rounded-xl text-xs font-bold uppercase tracking-widest transition-colors shadow-lg shadow-yellow-500/20">
                            <i class="fas fa-check mr-2"></i> Marcar como Revisado
                        </button>
                        @endif
                        
                        @if(in_array(Auth::user()->role, ['admin', 'ceo']) && $brief->status === 'reviewed')
                        <button type="button" onclick="approveBrief()"
                            class="px-6 py-3 bg-green-500 hover:bg-green-600 text-white rounded-xl text-xs font-bold uppercase tracking-widest transition-colors shadow-lg shadow-green-500/20">
                            <i class="fas fa-check-double mr-2"></i> Aprobar Brief
                        </button>
                        @endif
                        
                        <a href="{{ route('projects.show', $project) }}"
                            class="px-6 py-3 border border-gray-300 dark:border-white/10 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/5 rounded-xl text-xs font-bold uppercase tracking-widest transition-colors">
                            <i class="fas fa-times mr-2"></i> Cancelar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function saveDraft() {
        const form = document.getElementById('briefForm');
        const formData = new FormData(form);
        
        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Brief guardado como borrador', 'success');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error al guardar', 'error');
        });
    }
    
    function submitBrief() {
        if (confirm('¿Estás seguro de enviar este brief para revisión? Una vez enviado no podrás editarlo hasta que sea revisado.')) {
            const form = document.getElementById('briefForm');
            const formData = new FormData(form);
            formData.append('action', 'submit');
            
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Brief enviado para revisión', 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error al enviar', 'error');
            });
        }
    }
    
    function reviewBrief() {
        if (confirm('¿Marcar este brief como revisado?')) {
            const form = document.getElementById('briefForm');
            const formData = new FormData(form);
            formData.append('action', 'review');
            
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Brief marcado como revisado', 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error al revisar', 'error');
            });
        }
    }
    
    function approveBrief() {
        if (confirm('¿Aprobar este brief? Esto marcará el brief como finalizado y listo para ejecución.')) {
            const form = document.getElementById('briefForm');
            const formData = new FormData(form);
            formData.append('action', 'approve');
            
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Brief aprobado exitosamente', 'success');
                    setTimeout(() => {
                        window.location.href = '{{ route('projects.show', $project) }}';
                    }, 1500);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error al aprobar', 'error');
            });
        }
    }
    
    function showNotification(message, type = 'info') {
        // Implementar notificación según el sistema existente
        alert(message);
    }
    
    // Auto-save cada 30 segundos
    let autoSaveTimer;
    function setupAutoSave() {
        autoSaveTimer = setInterval(() => {
            if (document.hasFocus()) {
                saveDraft();
            }
        }, 30000);
    }
    
    // Iniciar auto-save cuando la página cargue
    document.addEventListener('DOMContentLoaded', function() {
        setupAutoSave();
        
        // Limpiar timer cuando se cierre la página
        window.addEventListener('beforeunload', function() {
            clearInterval(autoSaveTimer);
        });
    });
</script>
@endsection