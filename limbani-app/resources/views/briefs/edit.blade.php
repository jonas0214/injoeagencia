@extends('layouts.asana')

@section('content')
<style>
    @media (min-width: 768px) { aside { display: none !important; } main { margin-left: 0 !important; width: 100% !important; max-width: 100% !important; } }
    .wizard-step-enter { transition: all 0.4s ease-out; opacity: 0; transform: translateX(20px); }
    .wizard-step-enter-active { opacity: 1; transform: translateX(0); }
</style>

<div x-data="briefWizard()" class="flex h-screen w-full bg-white dark:bg-[#0f1012] text-gray-800 dark:text-white overflow-hidden relative" x-cloak>
    
    <!-- Sidebar de Pasos (Desktop) -->
    <div class="hidden lg:flex w-80 border-r border-gray-200 dark:border-white/5 bg-gray-50/50 dark:bg-[#111] flex-col shrink-0">
        <div class="p-8">
            <div class="flex items-center gap-3 mb-8">
                <a href="{{ route('projects.show', $project) }}" class="text-orange-500 hover:text-orange-600 transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <span class="text-xs font-bold uppercase tracking-widest text-gray-400">Volver al Proyecto</span>
            </div>
            <h2 class="text-2xl font-bold tracking-tight mb-2">Brief Mensual</h2>
            <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">Personaliza tu estrategia para este mes respondiendo estas 9 secciones clave.</p>
        </div>

        <nav class="flex-1 overflow-y-auto custom-scroll px-4 space-y-2 pb-8">
            <template x-for="(s, index) in steps" :key="index">
                <button @click="step = index + 1" 
                    class="w-full flex items-center gap-4 p-4 rounded-2xl transition-all duration-300 group"
                    :class="step === index + 1 ? 'bg-orange-500 text-white shadow-lg shadow-orange-500/20' : 'hover:bg-white dark:hover:bg-white/5 text-gray-500 dark:text-gray-400'">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold border"
                        :class="step === index + 1 ? 'border-white/20 bg-white/10' : 'border-gray-200 dark:border-white/10 group-hover:border-orange-500/50'">
                        <span x-text="index + 1"></span>
                    </div>
                    <span class="text-xs font-bold uppercase tracking-widest text-left" x-text="s.title"></span>
                </button>
            </template>
        </nav>
    </div>

    <!-- Contenido Principal -->
    <div class="flex-1 flex flex-col min-w-0 relative">
        <!-- Header Móvil / Barra de Progreso -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-white/5 flex flex-col gap-4 bg-white/80 dark:bg-[#0f1012]/80 backdrop-blur-xl z-20">
            <div class="flex justify-between items-center">
                <div class="lg:hidden flex items-center gap-3">
                    <a href="{{ route('projects.show', $project) }}" class="text-orange-500"><i class="fas fa-arrow-left"></i></a>
                    <span class="text-xs font-bold uppercase tracking-widest" x-text="steps[step-1].title"></span>
                </div>
                <div class="hidden lg:block text-xs font-medium text-gray-500 uppercase tracking-[0.2em]">
                    Paso <span x-text="step"></span> de <span x-text="steps.length"></span>
                </div>
                <div class="flex items-center gap-3 text-[10px] font-bold uppercase tracking-widest">
                    <span x-show="isSaving" class="text-orange-500 flex items-center gap-2">
                        <i class="fas fa-spinner fa-spin"></i> Guardando...
                    </span>
                    <span x-show="!isSaving && lastSaved" class="text-green-500 flex items-center gap-2">
                        <i class="fas fa-check"></i> Autoguardado <span x-text="lastSaved"></span>
                    </span>
                </div>
            </div>
            <!-- Progress Bar -->
            <div class="w-full h-1.5 bg-gray-100 dark:bg-white/5 rounded-full overflow-hidden">
                <div class="h-full bg-orange-500 transition-all duration-500 ease-out" :style="`width: ${(step/steps.length)*100}%`"></div    >
            </div>
        </div>

        <!-- Formulario Wizard -->
        <form id="briefForm" action="{{ route('briefs.update', $project) }}" method="POST" class="flex-1 overflow-y-auto custom-scroll p-6 md:p-12 lg:p-20" @submit.prevent="submitForm">
            @csrf
            @method('PUT')
            
            <div class="max-w-3xl mx-auto">
                <template x-if="true">
                    <div>
                        <!-- SECCIÓN 1: PRIORIDADES -->
                        <div x-show="step === 1" x-transition.opacity.duration.400ms>
                            <div class="mb-12">
                                <h3 class="text-3xl font-bold mb-4">Prioridades del Mes 🎯</h3>
                                <p class="text-lg text-gray-500 dark:text-gray-400">Define lo más relevante para tu marca este periodo.</p>
                            </div>
                            <div class="space-y-10">
                                <div class="data-box p-8 rounded-3xl bg-gray-50 dark:bg-white/[0.02] border border-gray-200 dark:border-white/10">
                                    <label class="block text-sm font-bold uppercase tracking-widest text-gray-400 mb-4">1. ¿Lanzamientos, promociones o novedades importantes?</label>
                                    <textarea name="answers[q1]" rows="4" class="w-full bg-transparent border-0 p-0 text-xl focus:ring-0 placeholder-gray-300 dark:placeholder-white/10" placeholder="Ej: Nuevos productos, aperturas, eventos...">{{ old('answers.q1', $brief->answers['q1'] ?? '') }}</textarea>
                                </div>
                                <div class="data-box p-8 rounded-3xl bg-gray-50 dark:bg-white/[0.02] border border-gray-200 dark:border-white/10">
                                    <label class="block text-sm font-bold uppercase tracking-widest text-gray-400 mb-4">2. ¿Qué producto/servicio necesita mayor visibilidad?</label>
                                    <textarea name="answers[q2]" rows="3" class="w-full bg-transparent border-0 p-0 text-xl focus:ring-0 placeholder-gray-300 dark:placeholder-white/10" placeholder="Ej: Nuestro servicio premium de consultoría...">{{ old('answers.q2', $brief->answers['q2'] ?? '') }}</textarea>
                                </div>
                                <div class="data-box p-8 rounded-3xl bg-gray-50 dark:bg-white/[0.02] border border-gray-200 dark:border-white/10">
                                    <label class="block text-sm font-bold uppercase tracking-widest text-gray-400 mb-4">3. Objetivo comercial principal</label>
                                    <select name="answers[q3]" class="w-full bg-transparent border-0 p-0 text-xl focus:ring-0">
                                        <option value="">Selecciona uno...</option>
                                        <option value="leads" {{ ($brief->answers['q3'] ?? '') == 'leads' ? 'selected' : '' }}>Generar más clientes potenciales</option>
                                        <option value="ventas" {{ ($brief->answers['q3'] ?? '') == 'ventas' ? 'selected' : '' }}>Aumentar ventas</option>
                                        <option value="branding" {{ ($brief->answers['q3'] ?? '') == 'branding' ? 'selected' : '' }}>Posicionar marca</option>
                                        <option value="lanzamiento" {{ ($brief->answers['q3'] ?? '') == 'lanzamiento' ? 'selected' : '' }}>Lanzar producto</option>
                                        <option value="comunidad" {{ ($brief->answers['q3'] ?? '') == 'comunidad' ? 'selected' : '' }}>Fortalecer comunidad</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- SECCIÓN 2: MENSAJE -->
                        <div x-show="step === 2" x-transition.opacity.duration.400ms>
                            <div class="mb-12">
                                <h3 class="text-3xl font-bold mb-4">Mensaje Estratégico 📣</h3>
                                <p class="text-lg text-gray-500 dark:text-gray-400">¿Qué quieres decirle al mundo este mes?</p>
                            </div>
                            <div class="space-y-10">
                                <div class="data-box p-8 rounded-3xl bg-gray-50 dark:bg-white/[0.02] border border-gray-200 dark:border-white/10">
                                    <label class="block text-sm font-bold uppercase tracking-widest text-gray-400 mb-4">4. Mensaje principal a comunicar</label>
                                    <textarea name="answers[q4]" rows="4" class="w-full bg-transparent border-0 p-0 text-xl focus:ring-0 placeholder-gray-300 dark:placeholder-white/10" placeholder="Ej: Nueva promoción, innovación, beneficios...">{{ old('answers.q4', $brief->answers['q4'] ?? '') }}</textarea>
                                </div>
                                <div class="data-box p-8 rounded-3xl bg-gray-50 dark:bg-white/[0.02] border border-gray-200 dark:border-white/10">
                                    <label class="block text-sm font-bold uppercase tracking-widest text-gray-400 mb-4">5. Campañas internas o anuncios importantes</label>
                                    <textarea name="answers[q5]" rows="3" class="w-full bg-transparent border-0 p-0 text-xl focus:ring-0 placeholder-gray-300 dark:placeholder-white/10" placeholder="Ej: Cambios en horarios, nuevas sedes, anuncios...">{{ old('answers.q5', $brief->answers['q5'] ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- SECCIÓN 3: PRODUCTOS -->
                        <div x-show="step === 3" x-transition.opacity.duration.400ms>
                            <div class="mb-12">
                                <h3 class="text-3xl font-bold mb-4">Productos a Destacar 💎</h3>
                                <p class="text-lg text-gray-500 dark:text-gray-400">Seleccionemos las estrellas del catálogo.</p>
                            </div>
                            <div class="space-y-10">
                                <div class="data-box p-8 rounded-3xl bg-gray-50 dark:bg-white/[0.02] border border-gray-200 dark:border-white/10">
                                    <label class="block text-sm font-bold uppercase tracking-widest text-gray-400 mb-4">6. Enumera productos/servicios a promocionar</label>
                                    <textarea name="answers[q6]" rows="3" class="w-full bg-transparent border-0 p-0 text-xl focus:ring-0 placeholder-gray-300 dark:placeholder-white/10">{{ old('answers.q6', $brief->answers['q6'] ?? '') }}</textarea>
                                </div>
                                <div class="data-box p-8 rounded-3xl bg-gray-50 dark:bg-white/[0.02] border border-gray-200 dark:border-white/10">
                                    <label class="block text-sm font-bold uppercase tracking-widest text-gray-400 mb-4">7. Prioridad máxima de venta</label>
                                    <textarea name="answers[q7]" rows="2" class="w-full bg-transparent border-0 p-0 text-xl focus:ring-0 placeholder-gray-300 dark:placeholder-white/10">{{ old('answers.q7', $brief->answers['q7'] ?? '') }}</textarea>
                                </div>
                                <div class="data-box p-8 rounded-3xl bg-gray-50 dark:bg-white/[0.02] border border-gray-200 dark:border-white/10">
                                    <label class="block text-sm font-bold uppercase tracking-widest text-gray-400 mb-4">8. ¿Existe alguna promoción o descuento?</label>
                                    <textarea name="answers[q8]" rows="3" class="w-full bg-transparent border-0 p-0 text-xl focus:ring-0 placeholder-gray-300 dark:placeholder-white/10" placeholder="Ej: 20% OFF en primera compra, Cupón BIENVENIDA...">{{ old('answers.q8', $brief->answers['q8'] ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- SECCIÓN 4: FECHAS -->
                        <div x-show="step === 4" x-transition.opacity.duration.400ms>
                            <div class="mb-12">
                                <h3 class="text-3xl font-bold mb-4">Fechas y Oportunidades 📅</h3>
                                <p class="text-lg text-gray-500 dark:text-gray-400">El calendario es tu mejor aliado.</p>
                            </div>
                            <div class="space-y-10">
                                <div class="data-box p-8 rounded-3xl bg-gray-50 dark:bg-white/[0.02] border border-gray-200 dark:border-white/10">
                                    <label class="block text-sm font-bold uppercase tracking-widest text-gray-400 mb-4">9. Fechas especiales o eventos del mes</label>
                                    <textarea name="answers[q9]" rows="4" class="w-full bg-transparent border-0 p-0 text-xl focus:ring-0 placeholder-gray-300 dark:placeholder-white/10" placeholder="Ej: Aniversarios, días internacionales, festividades...">{{ old('answers.q9', $brief->answers['q9'] ?? '') }}</textarea>
                                </div>
                                <div class="data-box p-8 rounded-3xl bg-gray-50 dark:bg-white/[0.02] border border-gray-200 dark:border-white/10">
                                    <label class="block text-sm font-bold uppercase tracking-widest text-gray-400 mb-4">10. Casos de éxito o experiencias a destacar</label>
                                    <textarea name="answers[q10]" rows="3" class="w-full bg-transparent border-0 p-0 text-xl focus:ring-0 placeholder-gray-300 dark:placeholder-white/10">{{ old('answers.q10', $brief->answers['q10'] ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- SECCIÓN 5: CONTENIDO -->
                        <div x-show="step === 5" x-transition.opacity.duration.400ms>
                            <div class="mb-12">
                                <h3 class="text-3xl font-bold mb-4">Contenido Estratégico 🎨</h3>
                                <p class="text-lg text-gray-500 dark:text-gray-400">Transformemos los retos en contenido de valor.</p>
                            </div>
                            <div class="space-y-10">
                                <div class="data-box p-8 rounded-3xl bg-gray-50 dark:bg-white/[0.02] border border-gray-200 dark:border-white/10">
                                    <label class="block text-sm font-bold uppercase tracking-widest text-gray-400 mb-4">11. ¿Qué tipo de contenido priorizar?</label>
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-6">
                                        <template x-for="type in contentTypes">
                                            <label class="relative flex flex-col items-center gap-3 p-6 rounded-2xl border border-gray-200 dark:border-white/10 cursor-pointer overflow-hidden group transition-all"
                                                :class="selectedContentTypes.includes(type.id) ? 'bg-orange-500 text-white border-orange-500' : 'bg-white dark:bg-white/5'">
                                                <input type="checkbox" name="answers[q11][]" :value="type.id" x-model="selectedContentTypes" class="hidden">
                                                <i class="text-2xl" :class="type.icon"></i>
                                                <span class="text-[10px] font-bold uppercase tracking-widest text-center" x-text="type.label"></span>
                                            </label>
                                        </template>
                                    </div>
                                </div>
                                <div class="data-box p-8 rounded-3xl bg-gray-50 dark:bg-white/[0.02] border border-gray-200 dark:border-white/10">
                                    <label class="block text-sm font-bold uppercase tracking-widest text-gray-400 mb-4">12. Preguntas frecuentes a responder</label>
                                    <textarea name="answers[q12]" rows="3" class="w-full bg-transparent border-0 p-0 text-xl focus:ring-0 placeholder-gray-300 dark:placeholder-white/10">{{ old('answers.q12', $brief->answers['q12'] ?? '') }}</textarea>
                                </div>
                                <div class="data-box p-8 rounded-3xl bg-gray-50 dark:bg-white/[0.02] border border-gray-200 dark:border-white/10">
                                    <label class="block text-sm font-bold uppercase tracking-widest text-gray-400 mb-4">13. Temas a EVITAR este mes</label>
                                    <textarea name="answers[q13]" rows="2" class="w-full bg-transparent border-0 p-0 text-xl focus:ring-0 placeholder-gray-300 dark:placeholder-white/10" placeholder="Temas sensibles o que no apliquen...">{{ old('answers.q13', $brief->answers['q13'] ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- SECCIÓN 6: RECURSOS -->
                        <div x-show="step === 6" x-transition.opacity.duration.400ms>
                            <div class="mb-12">
                                <h3 class="text-3xl font-bold mb-4">Recursos Disponibles 📸</h3>
                                <p class="text-lg text-gray-500 dark:text-gray-400">¿Con qué materia prima contamos?</p>
                            </div>
                            <div class="space-y-10">
                                <div class="data-box p-8 rounded-3xl bg-gray-50 dark:bg-white/[0.02] border border-gray-200 dark:border-white/10">
                                    <label class="block text-sm font-bold uppercase tracking-widest text-gray-400 mb-4">14. ¿Cuentas con material gráfico/video?</label>
                                    <div class="space-y-3 mt-4">
                                        <template x-for="r in recursos">
                                            <label class="flex items-center gap-4 p-4 rounded-xl border border-gray-200 dark:border-white/10 cursor-pointer bg-white dark:bg-white/5">
                                                <input type="checkbox" name="answers[q14][]" :value="r" class="rounded border-gray-300 text-orange-500 focus:ring-orange-500">
                                                <span class="text-sm font-medium" x-text="r"></span>
                                            </label>
                                        </template>
                                    </div>
                                </div>
                                <div class="data-box p-8 rounded-3xl bg-gray-50 dark:bg-white/[0.02] border border-gray-200 dark:border-white/10">
                                    <label class="block text-sm font-bold uppercase tracking-widest text-gray-400 mb-4">15. Personas que pueden participar</label>
                                    <textarea name="answers[q15]" rows="2" class="w-full bg-transparent border-0 p-0 text-xl focus:ring-0">{{ old('answers.q15', $brief->answers['q15'] ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- SECCIÓN 7: CAMPAÑAS -->
                        <div x-show="step === 7" x-transition.opacity.duration.400ms>
                            <div class="mb-12">
                                <h3 class="text-3xl font-bold mb-4">Campañas Publicitarias 🚀</h3>
                                <p class="text-lg text-gray-500 dark:text-gray-400">Inyectemos combustible a tu marca.</p>
                            </div>
                            <div class="space-y-10">
                                <div class="data-box p-8 rounded-3xl bg-gray-50 dark:bg-white/[0.02] border border-gray-200 dark:border-white/10">
                                    <label class="block text-sm font-bold uppercase tracking-widest text-gray-400 mb-4">16. ¿Deseas realizar pauta publicitaria (Meta Ads)?</label>
                                    <div class="flex gap-4 mt-4">
                                        <button type="button" @click="formData.answers['q16'] = 'si'" class="flex-1 py-4 rounded-xl border-2 font-bold uppercase tracking-widest text-xs transition-all" :class="formData.answers['q16'] === 'si' ? 'bg-orange-500 border-orange-500 text-white' : 'border-gray-200 dark:border-white/10 text-gray-400'">SÍ</button>
                                        <button type="button" @click="formData.answers['q16'] = 'no'" class="flex-1 py-4 rounded-xl border-2 font-bold uppercase tracking-widest text-xs transition-all" :class="formData.answers['q16'] === 'no' ? 'bg-gray-800 border-gray-800 text-white text-dark:bg-white dark:text-black dark:border-white' : 'border-gray-200 dark:border-white/10 text-gray-400'">NO</button>
                                        <input type="hidden" name="answers[q16]" x-model="formData.answers['q16']">
                                    </div>
                                </div>
                                <div class="data-box p-8 rounded-3xl bg-gray-50 dark:bg-white/[0.02] border border-gray-200 dark:border-white/10" x-show="formData.answers['q16'] === 'si'">
                                    <label class="block text-sm font-bold uppercase tracking-widest text-gray-400 mb-4">17. Presupuesto total de pauta</label>
                                    <div class="flex items-center gap-4">
                                        <span class="text-3xl font-bold text-orange-500">$</span>
                                        <input type="number" name="answers[q17]" class="w-full bg-transparent border-0 p-0 text-3xl font-bold focus:ring-0" placeholder="0.00" value="{{ old('answers.q17', $brief->answers['q17'] ?? '') }}">
                                    </div>
                                </div>
                                <div class="data-box p-8 rounded-3xl bg-gray-50 dark:bg-white/[0.02] border border-gray-200 dark:border-white/10" x-show="formData.answers['q16'] === 'si'">
                                    <label class="block text-sm font-bold uppercase tracking-widest text-gray-400 mb-4">18. Distribución del presupuesto</label>
                                    <textarea name="answers[q18]" rows="3" class="w-full bg-transparent border-0 p-0 text-xl focus:ring-0" placeholder="Ej: Producto A: 60%, Producto B: 40%">{{ old('answers.q18', $brief->answers['q18'] ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- SECCIÓN 8: RESULTADOS -->
                        <div x-show="step === 8" x-transition.opacity.duration.400ms>
                            <div class="mb-12">
                                <h3 class="text-3xl font-bold mb-4">Resultados Esperados ⭐</h3>
                                <p class="text-lg text-gray-500 dark:text-gray-400">¿Qué te gustaría lograr al final del mes?</p>
                            </div>
                            <div class="space-y-10">
                                <div class="data-box p-8 rounded-3xl bg-gray-50 dark:bg-white/[0.02] border border-gray-200 dark:border-white/10">
                                    <label class="block text-sm font-bold uppercase tracking-widest text-gray-400 mb-4">19. Resultado ideal tras la estrategia</label>
                                    <textarea name="answers[q19]" rows="6" class="w-full bg-transparent border-0 p-0 text-xl focus:ring-0 placeholder-gray-300 dark:placeholder-white/10" placeholder="Ej: Más ventas, más mensajes, más reservas, visibilidad...">{{ old('answers.q19', $brief->answers['q19'] ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- SECCIÓN 9: CIERRE -->
                        <div x-show="step === 9" x-transition.opacity.duration.400ms>
                            <div class="mb-12">
                                <h3 class="text-3xl font-bold mb-4">Información Adicional 📝</h3>
                                <p class="text-lg text-gray-500 dark:text-gray-400">Cualquier detalle extra que debamos saber.</p>
                            </div>
                            <div class="space-y-10">
                                <div class="data-box p-8 rounded-3xl bg-gray-50 dark:bg-white/[0.02] border border-gray-200 dark:border-white/10">
                                    <label class="block text-sm font-bold uppercase tracking-widest text-gray-400 mb-4">20. Observaciones finales</label>
                                    <textarea name="answers[q20]" rows="6" class="w-full bg-transparent border-0 p-0 text-xl focus:ring-0">{{ old('answers.q20', $brief->answers['q20'] ?? '') }}</textarea>
                                </div>

                                <div class="bg-orange-500/10 border border-orange-500/20 p-8 rounded-3xl">
                                    <p class="text-sm font-medium text-orange-600 dark:text-orange-400 leading-relaxed italic">
                                        👉 "Te agradeceríamos enviarnos tus respuestas antes del 17 de Marzo, para poder desarrollar la estrategia, producción de contenidos y campañas del mes."
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </form>

        <!-- Footer Navegación -->
        <div class="px-8 py-6 border-t border-gray-200 dark:border-white/5 bg-white/80 dark:bg-[#0f1012]/80 backdrop-blur-xl shrink-0">
            <div class="max-w-3xl mx-auto flex justify-between items-center">
                <button type="button" @click="prevStep" x-show="step > 1" class="px-8 py-4 text-xs font-bold uppercase tracking-widest text-gray-500 hover:text-black dark:hover:text-white transition-all">Anterior</button>
                <div x-show="step === 1" class="hidden md:block"></div>
                
                <div class="flex gap-4">
                    <button type="button" @click="step < steps.length ? nextStep() : submitBrief()"
                        class="px-10 py-4 bg-orange-500 hover:bg-orange-600 text-white rounded-2xl text-xs font-bold uppercase tracking-widest transition-all shadow-xl shadow-orange-500/20 active:scale-95"
                        x-text="step < steps.length ? 'Siguiente' : 'Enviar Brief Final'">
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function briefWizard() {
        return {
            step: 1,
            isSaving: false,
            lastSaved: null,
            formData: {
                answers: {
                    q16: '{{ $brief->answers['q16'] ?? '' }}'
                }
            },
            selectedContentTypes: @json($brief->answers['q11'] ?? []),
            contentTypes: [
                { id: 'edu', label: 'Educativo', icon: 'fas fa-graduation-cap' },
                { id: 'promo', label: 'Promocional', icon: 'fas fa-tag' },
                { id: 'testi', label: 'Testimonios', icon: 'fas fa-star' },
                { id: 'bts', label: 'Detrás de Escena', icon: 'fas fa-camera' },
                { id: 'demo', label: 'Demostración', icon: 'fas fa-play-circle' }
            ],
            recursos: ['Fotografías', 'Videos', 'Testimonios', 'Otro'],
            steps: [
                { title: 'Prioridades' },
                { title: 'Mensaje' },
                { title: 'Productos' },
                { title: 'Calendario' },
                { title: 'Contenido' },
                { title: 'Recursos' },
                { title: 'Publicidad' },
                { title: 'Resultados' },
                { title: 'Cierre' }
            ],
            nextStep() {
                this.saveDraft();
                this.step++;
                document.querySelector('form').scrollTop = 0;
            },
            prevStep() {
                this.step--;
                document.querySelector('form').scrollTop = 0;
            },
            saveDraft() {
                this.isSaving = true;
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
                .then(r => r.json())
                .then(data => {
                    this.isSaving = false;
                    this.lastSaved = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                })
                .catch(() => this.isSaving = false);
            },
            submitBrief() {
                if (confirm('¿Estás listo para enviar el brief final de este mes?')) {
                    const form = document.getElementById('briefForm');
                    const formData = new FormData(form);
                    formData.append('action', 'submit');
                    
                    this.isSaving = true;
                    fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    })
                    .then(r => r.json())
                    .then(data => {
                        window.location.href = '{{ route("projects.show", $project) }}';
                    });
                }
            }
        }
    }
</script>
@endsection