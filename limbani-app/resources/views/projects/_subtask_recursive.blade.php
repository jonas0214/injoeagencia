@php
    $teamMemberId = Auth::user()->teamMember ? Auth::user()->teamMember->id : null;
    $isCollab = Auth::user()->role === 'colaborador';
    
    // Función recursiva para verificar si la tarea debe ser visible
    if (!isset($shouldShow)) {
        $shouldShow = function($s, $tmId) use (&$shouldShow) {
            if ($s->team_member_id == $tmId) return true;
            foreach ($s->children as $child) {
                if ($shouldShow($child, $tmId)) return true;
            }
            return false;
        };
    }
@endphp

@foreach($subtasks as $subtask)
    @php 
        $level = $level ?? 0;
        $isVisible = !$isCollab || $shouldShow($subtask, $teamMemberId);
    @endphp

    @if($isVisible)
    <div x-data="{
        showChildren: localStorage.getItem('task_children_{{ $subtask->id }}') === 'true' ? true : false,
        toggleChildren() { this.showChildren = !this.showChildren; localStorage.setItem('task_children_{{ $subtask->id }}', this.showChildren); }
    }" class="border-b border-gray-100 dark:border-white/[0.03]">
        <div @click="openTaskPanel(@js($subtask), @js($section->title), @js($subtask->parent ? $subtask->parent->title : ''))" 
             class="group grid grid-cols-12 gap-4 {{ $level == 0 ? 'py-2 px-4' : 'py-1.5 px-4' }} hover:bg-gray-50 dark:hover:bg-white/[0.03] transition-all cursor-pointer items-center">
            
            <div class="col-span-7 flex items-center gap-3" style="padding-left: {{ $level * 20 }}px">
                @if($subtask->children->count() > 0)
                    <button @click.stop="toggleChildren()" class="w-4 h-4 flex items-center justify-center text-gray-600 hover:text-white transition-colors">
                        <i class="fas fa-caret-right text-[10px] transition-transform" :class="showChildren ? 'rotate-90' : ''"></i>
                    </button>
                @else 
                    <div class="w-4"></div> 
                @endif
                
                <input type="checkbox" 
                       :checked="{{ $subtask->is_completed ? 'true' : 'false' }}" 
                       @change="fetch('{{ url('/subtasks') }}/{{ $subtask->id }}', { method: 'PUT', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify({ is_completed: $event.target.checked }) })"
                       class="{{ $level == 0 ? 'w-4 h-4 border-gray-600' : 'w-3 h-3 border-gray-700' }} rounded-sm bg-transparent checked:bg-green-500">
                
                <span class="{{ $level == 0 ? 'text-[15px] font-medium text-gray-700 dark:text-gray-200' : 'text-xs text-gray-500' }} {{ $subtask->is_completed ? 'line-through opacity-40' : '' }}">
                    {{ $subtask->title }}
                </span>
            </div>

            <div class="col-span-3">
                @if($subtask->team_member_id)
                    <div class="flex items-center gap-2.5">
                        @if($subtask->teamMember && $subtask->teamMember->photo)
                            <img src="{{ asset('storage/' . $subtask->teamMember->photo) }}" class="{{ $level == 0 ? 'w-7 h-7' : 'w-5 h-5' }} rounded-full object-cover border border-white/10">
                        @else
                            <div class="{{ $level == 0 ? 'w-7 h-7 text-[10px]' : 'w-5 h-5 text-[8px]' }} rounded-full bg-orange-500 text-black flex items-center justify-center font-bold">
                                {{ substr($subtask->teamMember->name ?? '?', 0, 1) }}
                            </div>
                        @endif
                        <span class="{{ $level == 0 ? 'text-[13px]' : 'text-[9px]' }} text-gray-400 font-medium truncate max-w-[100px]">
                            {{ $subtask->teamMember->name ?? '' }}
                        </span>
                    </div>
                @elseif($level == 0)
                    <div class="flex items-center gap-2.5 text-gray-600">
                        <div class="w-7 h-7 rounded-full border border-dashed border-gray-700 flex items-center justify-center text-[10px]">
                            <i class="fas fa-user"></i>
                        </div>
                        <span class="text-[12px] italic">Sin asignar</span>
                    </div>
                @endif
            </div>

            <div class="col-span-2 text-right flex items-center justify-end gap-3">
                @php
                    $now = now();
                    $start = $subtask->start_date;
                    $due = $subtask->due_date;
                    $isOverdue = $due && $now > $due && !$subtask->is_completed;
                @endphp

                <!-- Indicador de Vencida (Solo si aplica) -->
                @if($isOverdue)
                    <div class="flex items-center px-2 py-0.5 rounded-full border border-red-500/30 bg-red-500/20 text-red-500 text-[8px] font-black uppercase tracking-tighter shrink-0 animate-pulse">
                        <div class="w-1.5 h-1.5 rounded-full bg-red-500 mr-1.5 shadow-[0_0_5px_rgba(255,0,0,0.5)]"></div>
                        Vencida
                    </div>
                @endif

                <div class="flex flex-col items-end min-w-[100px] leading-none gap-1">
                    @if($start)
                        <div class="flex items-center gap-1">
                            <span class="text-[8px] font-bold text-gray-500 uppercase">Inicia:</span>
                            <span class="text-[9px] font-bold text-orange-500/80">{{ $start->format('d M, h:i A') }}</span>
                        </div>
                    @endif
                    <div class="flex items-center gap-1">
                        <span class="text-[8px] font-bold text-gray-500 uppercase">Fin:</span>
                        <span class="{{ $level == 0 ? 'text-[11px]' : 'text-[9px]' }} font-black text-gray-400 dark:text-gray-300">
                            {{ $due ? $due->format('d M, h:i A') : '--' }}
                        </span>
                    </div>
                </div>
                
                @if($level > 0 && Auth::user()->role !== 'colaborador')
                    <button type="button" 
                            @click.stop="if(confirm('¿Borrar?')) fetch('{{ url('/subtasks') }}/{{ $subtask->id }}', { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } }).then(() => window.location.reload())" 
                            class="opacity-0 group-hover:opacity-100 text-gray-600 hover:text-red-500 p-1 transition-opacity">
                        <i class="fas fa-trash-alt text-[10px]"></i>
                    </button>
                @endif
            </div>
        </div>

        @if($subtask->children->count() > 0)
            <div x-show="showChildren" x-collapse>
                @include('projects._subtask_recursive', ['subtasks' => $subtask->children, 'level' => $level + 1, 'section' => $section])
            </div>
        @endif
    </div>
    @endif
@endforeach
