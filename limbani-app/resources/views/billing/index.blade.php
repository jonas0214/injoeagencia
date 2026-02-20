@extends('layouts.asana')

@section('content')
<div class="max-w-[1600px] mx-auto px-8 py-12" x-data="billingHandler()">
    
    <div class="mb-12 border-b border-gray-200 dark:border-white/5 pb-8 flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h1 class="text-4xl font-light text-gray-800 dark:text-white tracking-wide mb-1">Cuentas de <span class="font-medium text-gray-900 dark:text-white">Cobro</span></h1>
            <p class="text-gray-500 text-xs font-medium tracking-[0.15em] uppercase">Genera tu cobro seleccionando tareas finalizadas y pendientes</p>
        </div>
        @if(Auth::user()->role === 'colaborador')
        <button @click="showGenerator = !showGenerator"
                class="bg-orange-600 hover:bg-orange-700 text-white px-8 py-4 rounded-2xl font-bold text-xs uppercase tracking-[0.2em] transition-all shadow-xl shadow-orange-900/20 border border-orange-500/20">
            <i class="fas fa-plus-circle mr-2 text-lg"></i> Nueva Cuenta de Cobro
        </button>
        @endif
    </div>

    <!-- Pestañas -->
    <div class="flex items-center gap-8 border-b border-gray-200 dark:border-white/5 mb-10 text-sm font-medium">
        <a href="{{ route('dashboard') }}" class="pb-4 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">Proyectos</a>
        <a href="{{ route('billing.index') }}" class="pb-4 text-gray-900 dark:text-white border-b-2 border-orange-500">Cuentas de Cobro</a>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-500/10 border border-green-500/20 rounded-xl text-green-500 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
        
        <!-- Columna Principal -->
        <div class="lg:col-span-8 space-y-8">
            
            @if(Auth::user()->role === 'colaborador')
            <div class="bg-white dark:bg-white/[0.03] backdrop-blur-md border border-gray-200 dark:border-white/10 rounded-2xl p-8 shadow-sm dark:shadow-none"
                 x-show="showGenerator"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform -translate-y-4"
                 x-transition:enter-end="opacity-100 transform translate-y-0">
                
                @if(isset($availableTasks) && $availableTasks->count() > 0)
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Generar Nueva Cuenta de Cobro</h3>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                        <span class="text-[10px] text-green-500 font-bold uppercase tracking-widest">Sistema de Cobros Activo</span>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mb-8 italic">Selecciona las tareas que deseas incluir en este cobro.</p>
                
                <form id="billingForm" action="{{ route('billing.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="space-y-3">
                        @foreach($availableTasks as $task)
                        @php
                            $isLate = $task->due_date && $task->due_date->isPast() && !$task->is_completed;
                        @endphp
                        <label class="flex items-center gap-4 p-4 rounded-2xl transition-all border cursor-pointer
                            {{ $isLate ? 'bg-red-500/5 border-red-500/20 opacity-80' : ($task->is_approved ? 'bg-green-500/5 border-green-500/20 hover:bg-green-500/10' : ($task->is_completed ? 'bg-gray-50 dark:bg-white/5 border-gray-100 dark:border-white/5 hover:bg-gray-100 dark:hover:bg-white/10' : 'bg-orange-500/5 border-orange-500/10 opacity-70')) }}">
                            
                            <div class="flex items-center justify-center w-4 h-4">
                                <input type="checkbox" name="task_ids[]" value="{{ $task->id }}"
                                       data-title="{{ $task->title }}"
                                       data-assigned="{{ $task->created_at->format('d/m/Y') }}"
                                       data-due="{{ $task->due_date ? $task->due_date->format('d/m/Y') : '--' }}"
                                       data-status="{{ $isLate ? 'Vencida' : ($task->is_approved ? 'Aprobada' : ($task->is_completed ? 'En Revisión' : 'Pendiente')) }}"
                                       data-late="{{ $isLate ? 'true' : 'false' }}"
                                       {{ $isLate ? 'checked onclick=return&nbsp;false;' : '' }}
                                       class="w-4 h-4 border-gray-600 rounded bg-transparent checked:bg-orange-500 focus:ring-0">
                            </div>

                            <div class="flex-1">
                                <p class="text-sm font-medium {{ ($task->is_approved || $isLate) ? 'text-gray-800 dark:text-white' : 'text-gray-500 dark:text-gray-400' }} {{ $isLate ? 'line-through' : '' }}">{{ $task->title }}</p>
                                <p class="text-[9px] text-gray-600 uppercase tracking-widest">{{ $task->task->project->name ?? 'General' }}</p>
                            </div>

                            <div class="flex flex-col items-end gap-1">
                                @if($isLate)
                                    <span class="px-2 py-0.5 rounded bg-red-500/20 text-red-500 text-[7px] font-black uppercase tracking-widest border border-red-500/30">Vencida (Automática)</span>
                                @elseif($task->is_approved)
                                    <span class="px-2 py-0.5 rounded bg-green-500/20 text-green-500 text-[7px] font-black uppercase tracking-widest border border-green-500/30">Lista para Cobro</span>
                                @elseif($task->is_completed)
                                    <span class="px-2 py-0.5 rounded bg-blue-500/10 text-blue-400 text-[7px] font-bold uppercase tracking-widest border border-blue-500/20 italic">En Revisión</span>
                                @else
                                    <span class="px-2 py-0.5 rounded bg-yellow-500/5 text-yellow-600 text-[7px] font-bold uppercase tracking-widest border border-yellow-500/10">Pendiente</span>
                                @endif
                            </div>
                        </label>
                        @endforeach
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6 border-t border-white/5">
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Valor Total a Cobrar ($)</label>
                            <input type="number" name="amount" x-model="amount" required step="0.01" placeholder="0.00" class="w-full bg-gray-50 dark:bg-[#111] border border-gray-200 dark:border-white/10 rounded-xl p-4 text-sm text-gray-900 dark:text-white focus:ring-1 focus:ring-orange-500 outline-none">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Concepto / Notas</label>
                            <input type="text" name="notes" x-model="notes" placeholder="Ej: Servicios profesionales mes actual" class="w-full bg-gray-50 dark:bg-[#111] border border-gray-200 dark:border-white/10 rounded-xl p-4 text-sm text-gray-900 dark:text-white focus:ring-1 focus:ring-orange-500 outline-none">
                        </div>
                    </div>

                    <button type="button"
                            @click="preview()"
                            class="w-full bg-orange-500/10 hover:bg-orange-500/20 text-orange-500 font-bold py-4 rounded-xl transition-all border border-orange-500/20 uppercase tracking-[0.2em] text-xs shadow-lg shadow-orange-900/5">
                        <i class="fas fa-eye mr-2"></i> Previsualizar Cuenta
                    </button>

                    <!-- MODAL PREVISUALIZACION -->
                    <div x-show="showPreview"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0 z-[99999] flex items-start justify-center p-4 bg-[#000000]/90 backdrop-blur-xl overflow-y-auto"
                         style="display: none;"
                         @keydown.escape.window="showPreview = false">
                        <div class="bg-[#1a1a1a] border border-white/10 rounded-[2.5rem] w-full max-w-2xl my-8 shadow-2xl relative z-[100000]" @click.away="showPreview = false" @click.stop>
                            <div class="p-10">
                                <div class="flex justify-between items-start mb-12">
                                    <div>
                                        <h2 class="text-2xl font-bold text-white tracking-tighter">CUENTA DE COBRO</h2>
                                        <p class="text-orange-500 text-[10px] font-black uppercase tracking-[0.3em] mt-1">Limbani Agency</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Fecha Emisión</p>
                                        <p class="text-sm text-white font-medium">{{ date('d / m / Y') }}</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-12 mb-12">
                                    <div>
                                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-2">De:</p>
                                        <p class="text-sm font-bold text-white">{{ Auth::user()->name }}</p>
                                        <p class="text-xs text-gray-400">{{ Auth::user()->email }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-2">Para:</p>
                                        <p class="text-sm font-bold text-white">RECURSOS HUMANOS</p>
                                        <p class="text-xs text-gray-400">Limbani Agency S.A.S</p>
                                    </div>
                                </div>

                                <div class="mb-12">
                                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-6">Detalle de Tareas Radicadas:</p>
                                    <div class="space-y-4">
                                        <template x-if="selectedTasks.length === 0">
                                            <p class="text-xs text-gray-500 italic">No hay tareas seleccionadas.</p>
                                        </template>
                                        <div class="space-y-4">
                                            <template x-for="(task, index) in selectedTasks" :key="index">
                                                <div class="p-4 rounded-2xl border border-white/5" :class="task.isLate ? 'bg-red-500/5 border-red-500/20' : 'bg-white/5 border-white/5'">
                                                    <div class="flex justify-between items-start mb-2">
                                                        <div>
                                                            <h4 class="text-sm font-bold text-gray-200" x-text="task.title"></h4>
                                                            <template x-if="task.isLate">
                                                                <p class="text-[7px] text-red-500 font-bold uppercase tracking-widest mt-1"><i class="fas fa-exclamation-triangle mr-1"></i> Tarea Vencida - Aplica Descuento</p>
                                                            </template>
                                                        </div>
                                                        <span class="text-[8px] font-black uppercase px-2 py-0.5 rounded"
                                                              :class="task.status === 'Aprobada' ? 'bg-green-500/20 text-green-500' : 'bg-yellow-500/20 text-yellow-500'"
                                                              x-text="task.status"></span>
                                                    </div>
                                                    <div class="grid grid-cols-2 gap-4">
                                                        <div>
                                                            <p class="text-[8px] font-bold text-gray-500 uppercase tracking-widest">Asignación</p>
                                                            <p class="text-[10px] text-gray-300" x-text="task.assigned"></p>
                                                        </div>
                                                        <div>
                                                            <p class="text-[8px] font-bold text-gray-500 uppercase tracking-widest">Vencimiento</p>
                                                            <p class="text-[10px] text-gray-300" x-text="task.due"></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                <!-- SECCIÓN INFORMATIVA: TAREAS PENDIENTES NO RADICADAS -->
                                <template x-if="unselectedTasks.length > 0">
                                    <div class="mb-12 pt-8 border-t border-white/5">
                                        <p class="text-[10px] font-bold text-gray-600 uppercase tracking-widest mb-6 italic">Tareas Pendientes por Radicar (Informativo):</p>
                                        <div class="space-y-4 opacity-50">
                                            <template x-for="task in unselectedTasks">
                                                <div class="p-4 rounded-2xl border border-dashed border-white/10 bg-white/[0.02]">
                                                    <div class="flex justify-between items-start">
                                                        <h4 class="text-xs font-medium text-gray-400" x-text="task.title"></h4>
                                                        <span class="text-[7px] font-black uppercase px-2 py-0.5 rounded bg-white/5 text-gray-500" x-text="task.status"></span>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>

                                <div class="bg-white/5 rounded-3xl p-8 space-y-4 mb-10">
                                    <div class="flex justify-between items-center border-b border-white/5 pb-4">
                                        <div>
                                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Subtotal Servicios</p>
                                            <p class="text-xs text-gray-400 italic" x-text="notes || 'Prestación de servicios'"></p>
                                        </div>
                                        <p class="text-xl font-light text-white">$<span x-text="new Intl.NumberFormat('es-CO').format(amount || 0)"></span></p>
                                    </div>
                                    
                                    <template x-if="calculateDiscount() > 0">
                                        <div class="flex justify-between items-center text-red-400">
                                            <p class="text-[10px] font-bold uppercase tracking-widest">
                                                Penalización por Retraso
                                                (<span x-text="selectedTasks.filter(t => t.isLate).length"></span> de <span x-text="selectedTasks.length"></span> tareas)
                                            </p>
                                            <p class="text-lg font-medium">-$<span x-text="new Intl.NumberFormat('es-CO').format(calculateDiscount())"></span></p>
                                        </div>
                                    </template>

                                    <div class="flex justify-between items-center pt-2">
                                        <p class="text-[10px] font-bold text-orange-500 uppercase tracking-[0.2em]">Total Neto a Radicar</p>
                                        <p class="text-4xl font-bold text-white leading-none">$<span x-text="new Intl.NumberFormat('es-CO').format(Math.max(0, (parseFloat(amount) || 0) - calculateDiscount()))"></span></p>
                                    </div>
                                </div>

                                <div class="flex gap-4">
                                    <button type="button" @click="showPreview = false" class="flex-1 py-4 rounded-2xl bg-white/5 border border-white/10 text-xs font-bold text-gray-400 uppercase tracking-widest hover:bg-white/10 transition-all">Regresar</button>
                                    <button type="button" @click="submitForm()" class="flex-[2] py-4 rounded-2xl bg-orange-600 text-white font-bold text-xs uppercase tracking-[0.2em] hover:bg-orange-700 transition-all shadow-xl shadow-orange-900/20">Radicar Cuenta</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                @else
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-check-double text-gray-600 text-xl"></i>
                    </div>
                    <h4 class="text-white font-medium mb-1">¡Todo al día!</h4>
                    <p class="text-gray-500 text-xs">No tienes tareas pendientes por cobrar en este momento.</p>
                </div>
                @endif
            </div>
            @endif

            <div class="bg-white dark:bg-white/[0.03] backdrop-blur-md border border-gray-200 dark:border-white/10 rounded-2xl p-8 shadow-sm dark:shadow-none">
                <h3 class="text-lg font-medium text-gray-800 dark:text-white mb-6">Historial de Cuentas</h3>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left border-b border-white/5">
                                <th class="pb-4 text-[10px] font-bold text-gray-500 uppercase tracking-widest">Referencia</th>
                                @if(Auth::user()->role !== 'colaborador')
                                <th class="pb-4 text-[10px] font-bold text-gray-500 uppercase tracking-widest">Colaborador</th>
                                @endif
                                <th class="pb-4 text-[10px] font-bold text-gray-500 uppercase tracking-widest">Fecha</th>
                                <th class="pb-4 text-[10px] font-bold text-gray-500 uppercase tracking-widest">Monto</th>
                                <th class="pb-4 text-right text-[10px] font-bold text-gray-500 uppercase tracking-widest">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($billings as $b)
                            <tr class="group hover:bg-white/[0.01] transition-colors cursor-pointer" @click="openDetail({
                                reference: '{{ $b->reference }}',
                                status: '{{ $b->status }}',
                                created_at: '{{ $b->created_at }}',
                                amount: {{ $b->amount }},
                                discount: {{ $b->discount ?? 0 }}
                            })">
                                <td class="py-4 text-sm font-medium text-gray-300">
                                    <div class="flex flex-col">
                                        <div class="flex items-center gap-2">
                                            <span>{{ $b->reference }}</span>
                                            <i class="fas fa-external-link-alt text-[10px] text-gray-600 group-hover:text-orange-500 transition-colors"></i>
                                        </div>
                                        <div class="flex flex-wrap gap-1 mt-1">
                                            @foreach(\App\Models\Subtask::whereIn('id', $b->task_ids)->get() as $t)
                                                <span class="text-[7px] px-1 py-0.5 rounded bg-white/5 text-gray-500 uppercase tracking-tighter" title="{{ $t->title }}">
                                                    {{ Str::limit($t->title, 15) }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                </td>
                                @if(Auth::user()->role !== 'colaborador')
                                <td class="py-4 text-sm text-gray-300">{{ $b->teamMember->name }}</td>
                                @endif
                                <td class="py-4 text-xs text-gray-500">{{ $b->billed_at->format('d/m/Y') }}</td>
                                <td class="py-4 text-sm font-black text-gray-900 dark:text-white">
                                    <div class="flex flex-col items-end">
                                        <span>${{ number_format($b->amount, 2) }}</span>
                                        @if($b->discount > 0)
                                            <span class="text-[7px] text-red-500 font-bold uppercase tracking-tighter">Desc: -${{ number_format($b->discount, 2) }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-4 text-right">
                                    @if(in_array(Auth::user()->role, ['admin', 'ceo', 'contabilidad']))
                                    <form action="{{ route('billing.status', $b) }}" method="POST" class="inline">
                                        @csrf @method('PATCH')
                                        <select name="status" onchange="this.form.submit()" class="bg-black/40 border border-white/10 rounded-lg text-[9px] font-bold p-2 text-gray-400 focus:ring-1 focus:ring-orange-500 outline-none uppercase tracking-tighter" @click.stop>
                                            <option value="pending" {{ $b->status == 'pending' ? 'selected' : '' }}>Pendiente</option>
                                            <option value="approved" {{ $b->status == 'approved' ? 'selected' : '' }}>Aprobada</option>
                                            <option value="paid" {{ $b->status == 'paid' ? 'selected' : '' }}>Pagada</option>
                                            <option value="rejected" {{ $b->status == 'rejected' ? 'selected' : '' }}>Rechazada</option>
                                        </select>
                                    </form>
                                    @else
                                    <span class="px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest
                                        {{ $b->status == 'pending' ? 'bg-yellow-500/10 text-yellow-500 border border-yellow-500/20' : '' }}
                                        {{ $b->status == 'approved' ? 'bg-blue-500/10 text-blue-500 border border-blue-500/20' : '' }}
                                        {{ $b->status == 'paid' ? 'bg-green-500/10 text-green-500 border border-green-500/20 shadow-[0_0_10px_rgba(34,197,94,0.1)]' : '' }}
                                        {{ $b->status == 'rejected' ? 'bg-red-500/10 text-red-500 border border-red-500/20' : '' }}
                                    ">
                                        {{ strtoupper($b->status) }}
                                    </span>
                                    @endif

                                    @if(in_array($b->status, ['pending', 'rejected']) || Auth::user()->role === 'admin')
                                    <form action="{{ route('billing.destroy', $b) }}" method="POST" class="inline ml-2" onsubmit="return confirm('¿Eliminar este registro de cobro? Esto liberará las actividades para un nuevo cobro.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 text-gray-600 hover:text-red-500 transition-colors" @click.stop>
                                            <i class="fas fa-trash-alt text-xs"></i>
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="py-12 text-center text-gray-600 italic text-xs">No se encontraron registros de cobro.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Lateral -->
        <div class="lg:col-span-4 space-y-8">
            <div class="bg-white/[0.03] backdrop-blur-md border border-white/10 rounded-2xl p-8 overflow-hidden relative">
                <div class="absolute -top-10 -right-10 w-32 h-32 bg-orange-600/5 rounded-full blur-3xl"></div>
                <h3 class="text-sm font-medium text-gray-800 dark:text-white uppercase tracking-[0.2em] mb-8 relative z-10">Resumen Financiero</h3>
                <div class="space-y-6 relative z-10">
                    <div class="p-6 rounded-2xl bg-green-500/5 border border-green-500/10 hover:bg-green-500/10 transition-colors">
                        <p class="text-[9px] text-gray-500 uppercase font-black tracking-widest mb-2">Total Cobrado (Pagado)</p>
                        <p class="text-3xl font-light text-gray-900 dark:text-white leading-none">
                            <span class="text-green-500 text-sm font-bold mr-1">$</span>{{ number_format($billings->where('status', 'paid')->sum('amount'), 2) }}
                        </p>
                    </div>
                    <div class="p-6 rounded-2xl bg-yellow-500/5 border border-yellow-500/10 hover:bg-yellow-500/10 transition-colors">
                        <p class="text-[9px] text-gray-500 uppercase font-black tracking-widest mb-2">Pendiente por Recibir</p>
                        <p class="text-3xl font-light text-gray-900 dark:text-white leading-none">
                            <span class="text-yellow-500 text-sm font-bold mr-1">$</span>{{ number_format($billings->where('status', 'pending')->sum('amount'), 2) }}
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="p-8 rounded-2xl bg-orange-600/5 border border-orange-600/10">
                <i class="fas fa-info-circle text-orange-500 mb-4"></i>
                <p class="text-xs text-gray-400 leading-relaxed">
                    Recuerda que las tareas seleccionadas en una cuenta de cobro no volverán a aparecer en la lista de tareas disponibles para futuros cobros.
                </p>
            </div>
        </div>
    </div>
    <!-- MODAL DETALLE DE CUENTA REGISTRADA -->
    <div x-show="showDetail"
         class="fixed inset-0 z-[99999] flex items-start justify-center p-4 bg-[#000000]/90 backdrop-blur-xl overflow-y-auto"
         style="display: none;"
         @keydown.escape.window="showDetail = false">
        
        <div class="bg-[#1a1a1a] border border-white/10 rounded-[2.5rem] w-full max-w-2xl my-8 shadow-2xl relative" @click.away="showDetail = false" @click.stop>
            <div class="p-10" x-show="currentDetail">
                <div class="flex justify-between items-start mb-12">
                    <div>
                        <h2 class="text-2xl font-bold text-white tracking-tighter">DETALLE DE COBRO</h2>
                        <p class="text-orange-500 text-[10px] font-black uppercase tracking-[0.3em] mt-1" x-text="currentDetail.reference"></p>
                    </div>
                    <button @click="showDetail = false" class="text-gray-500 hover:text-white transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <div class="grid grid-cols-2 gap-12 mb-12">
                    <div>
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-2">Estado:</p>
                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-white/10 text-white" x-text="currentDetail.status"></span>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-2">Fecha Registro:</p>
                        <p class="text-sm text-white" x-text="new Date(currentDetail.created_at).toLocaleDateString()"></p>
                    </div>
                </div>

                <div class="bg-white/5 rounded-3xl p-8 space-y-4 mb-10 border border-white/10">
                    <div class="flex justify-between items-center border-b border-white/5 pb-4">
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Subtotal</p>
                        <p class="text-xl font-light text-white" x-text="'$' + new Intl.NumberFormat('es-CO').format(currentDetail.amount)"></p>
                    </div>
                    
                    <template x-if="currentDetail.discount > 0">
                        <div class="flex justify-between items-center text-red-400">
                            <p class="text-[10px] font-bold uppercase tracking-widest">Descuento Aplicado</p>
                            <p class="text-lg font-medium" x-text="'-$' + new Intl.NumberFormat('es-CO').format(currentDetail.discount)"></p>
                        </div>
                    </template>

                    <div class="flex justify-between items-center pt-2">
                        <p class="text-[10px] font-bold text-orange-500 uppercase tracking-[0.2em]">Total Neto</p>
                        <p class="text-4xl font-bold text-white tracking-tighter" x-text="'$' + new Intl.NumberFormat('es-CO').format(currentDetail.amount - (currentDetail.discount || 0))"></p>
                    </div>
                </div>

                <div class="flex gap-4">
                    <button @click="showDetail = false" class="w-full py-4 rounded-2xl bg-white/5 border border-white/10 text-xs font-bold text-gray-400 uppercase tracking-widest hover:bg-white/10 transition-all">Cerrar Detalle</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
         Alpine.data('billingHandler', () => ({
            showGenerator: {{ (isset($availableTasks) && $availableTasks->count() > 0) ? 'true' : 'false' }},
            showPreview: false,
            showDetail: false,
            currentDetail: {
                reference: '',
                status: '',
                created_at: '',
                amount: 0,
                discount: 0
            },
            amount: '',
            notes: '',
            selectedTasks: [],
            unselectedTasks: [],

            openDetail(billing) {
                console.log("Opening detail for:", billing.reference);
                this.currentDetail = billing;
                this.showDetail = true;
            },

            updateTaskLists() {
                console.log("Updating task lists...");
                let selected = [];
                // Seleccionar TODOS los inputs (visibles y bloqueados) del formulario
                const checkboxes = Array.from(document.querySelectorAll('#billingForm input[name="task_ids[]"]'));
                
                checkboxes.forEach(el => {
                    const taskData = {
                        title: el.getAttribute('data-title'),
                        assigned: el.getAttribute('data-assigned'),
                        due: el.getAttribute('data-due'),
                        status: el.getAttribute('data-status'),
                        isLate: el.getAttribute('data-late') === 'true'
                    };

                    if (el.checked) {
                        selected.push(taskData);
                    }
                });
                
                this.unselectedTasks = checkboxes
                    .filter(el => !el.checked)
                    .map(el => ({
                        title: el.getAttribute('data-title'),
                        assigned: el.getAttribute('data-assigned'),
                        due: el.getAttribute('data-due'),
                        status: el.getAttribute('data-status')
                    }));

                this.selectedTasks = [...selected];
                console.log("Selected Tasks count:", this.selectedTasks.length);
            },

            preview() {
                this.updateTaskLists();
                this.showPreview = true;
            },

            calculateDiscount() {
                const baseAmount = parseFloat(this.amount) || 0;
                if (baseAmount <= 0 || this.selectedTasks.length === 0) return 0;
                
                let lateTasksCount = this.selectedTasks.filter(t => t.isLate).length;
                let totalTasksCount = this.selectedTasks.length;
                
                // El porcentaje de descuento es (tareas vencidas / total de tareas en la cuenta)
                let discountPercentage = lateTasksCount / totalTasksCount;
                
                return (baseAmount * discountPercentage);
            },

            submitForm() {
                if(confirm('¿Confirmas que deseas radicar esta cuenta de cobro?')) {
                    const form = document.getElementById('billingForm');
                    form.submit();
                }
            }
        }));
    });
</script>
@endpush
