<!-- PANEL LATERAL DE TAREA (Compartido) -->
<div x-show="openPanel" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="translate-x-full"
     x-transition:enter-end="translate-x-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="translate-x-0"
     x-transition:leave-end="translate-x-full"
     class="fixed inset-y-0 right-0 w-full md:w-[650px] bg-[#1a1a1a] border-l border-white/10 shadow-2xl z-[100] flex flex-col" 
     style="display: none;">
    
    <div class="px-6 py-3 border-b border-white/5 flex justify-between items-center bg-[#1a1a1a]">
        <div class="flex items-center gap-4">
            <button @click="currentTask.is_completed = !currentTask.is_completed; updateTask().then(() => window.location.reload())" class="flex items-center gap-2 px-3 py-1.5 rounded-lg border transition-all text-[10px] font-bold uppercase tracking-widest" :class="currentTask.is_completed ? 'bg-green-500/10 border-green-500/50 text-green-500' : 'bg-white/5 border-white/10 text-gray-400'">
                <i class="fas fa-check-circle"></i><span x-text="currentTask.is_completed ? 'Finalizada' : 'Marcar Finalizada'"></span>
            </button>
            <div class="text-[10px] font-semibold text-gray-500 uppercase tracking-wider flex items-center gap-1">
                <span class="text-orange-500 truncate max-w-[80px]" x-text="currentTask.project_name"></span>
                <i class="fas fa-chevron-right text-[8px] opacity-30"></i>
                <span x-text="currentTask.section_title" class="truncate max-w-[80px]"></span>
                <template x-if="currentTask.parent_title">
                    <div class="flex items-center gap-1">
                        <i class="fas fa-chevron-right text-[8px] opacity-30"></i>
                        <span x-text="currentTask.parent_title" class="text-gray-400 truncate max-w-[100px]"></span>
                    </div>
                </template>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button @click="await updateTask(); window.location.reload();" class="bg-orange-500 text-black px-4 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-widest hover:bg-orange-600 transition-colors">
                <i class="fas fa-save mr-2"></i> Guardar Cambios
            </button>
            @if(Auth::user()->role !== 'colaborador')
            <button type="button" @click="if(confirm('¿Eliminar?')) fetch('{{ url('/subtasks') }}/'+currentTask.id, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } }).then(() => window.location.reload())" class="w-10 h-10 flex items-center justify-center text-gray-600 hover:text-red-500"><i class="fas fa-trash-alt text-sm"></i></button>
            @endif
            <button @click="openPanel = false" class="w-10 h-10 flex items-center justify-center text-gray-400 hover:text-white"><i class="fas fa-times text-lg"></i></button>
        </div>
    </div>

    <div class="flex-1 overflow-y-auto custom-scroll p-10 space-y-10">
        <textarea x-model="currentTask.title" rows="1" class="w-full bg-transparent border-none text-[32px] font-semibold text-white focus:ring-0 p-0 resize-none leading-tight tracking-tight"></textarea>
        
        <div class="grid grid-cols-1 gap-6 max-w-md">
            <div class="flex items-center gap-12">
                <label class="w-32 text-xs font-medium text-gray-500 uppercase tracking-wider">Responsable</label>
                <div class="flex-1">
                    @if(Auth::user()->role === 'colaborador')
                        <div class="flex items-center gap-2 px-2 py-1.5 bg-white/5 border border-white/10 rounded-lg">
                            <div class="w-5 h-5 rounded-full bg-orange-500 text-black flex items-center justify-center text-[8px] font-bold">
                                <span x-text="currentTask.team_member_name ? currentTask.team_member_name.substring(0, 1) : '?'"></span>
                            </div>
                            <span class="text-sm text-gray-300" x-text="currentTask.team_member_name || 'Sin asignar'"></span>
                        </div>
                    @else
                        <form @submit.prevent="updateTask()">
                            <select
                                name="team_member_id"
                                x-model="currentTask.team_member_id"
                                @change="updateTask()"
                                class="w-full bg-[#1a1a1a] border border-white/10 rounded-lg text-sm text-gray-300 focus:ring-1 focus:ring-orange-500 outline-none p-2"
                            >
                                <option value="">Sin asignar</option>
                                @foreach(\App\Models\TeamMember::orderBy('name')->get() as $m)
                                    <option value="{{ $m->id }}">{{ $m->name }}</option>
                                @endforeach
                            </select>
                        </form>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-12">
                <label class="w-32 text-xs font-medium text-gray-500 uppercase tracking-wider">Vencimiento</label>
                <div class="flex-1 flex flex-col gap-1">
                    @if(Auth::user()->role === 'colaborador')
                        <div class="flex items-center gap-2 px-1 py-1">
                            <i class="far fa-calendar-alt text-gray-500"></i>
                            <span class="text-sm text-gray-300" x-text="currentTask.due_date ? new Date(currentTask.due_date).toLocaleDateString('es-ES', { day: 'numeric', month: 'long' }) : 'Sin fecha'"></span>
                        </div>
                    @else
                        <div class="relative flex items-center group">
                            <i class="far fa-calendar-alt absolute left-0 text-gray-500 group-hover:text-orange-500 transition-colors"></i>
                            <input type="date" x-model="currentTask.due_date" class="bg-transparent border-none pl-8 text-sm text-gray-300 focus:ring-0 p-0 cursor-pointer hover:text-white transition-colors">
                        </div>
                    @endif
                    <template x-if="currentTask.due_date">
                        <p class="text-[10px] font-bold uppercase tracking-wider" :class="new Date(currentTask.due_date) < new Date() ? 'text-red-500' : 'text-green-500'"><i class="fas fa-clock mr-1"></i> <span x-text="getRemainingTime(currentTask.due_date)"></span></p>
                    </template>
                </div>
            </div>
        </div>

        <div class="space-y-3">
            <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Descripción</label>
            <textarea x-model="currentTask.description" rows="4" class="w-full bg-white/[0.02] border border-white/5 rounded-xl p-4 text-sm text-gray-300 placeholder-gray-600 focus:ring-1 focus:ring-orange-500 transition-all resize-none"></textarea>
        </div>

        <!-- SUBTAREAS -->
        <div class="space-y-4 pt-6 border-t border-white/5">
            <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Subtareas</label>
            <div class="space-y-2">
                <template x-for="child in (currentTask.children || [])" :key="child.id">
                    <div class="group flex items-center gap-3 p-2 rounded-lg bg-white/[0.02] border border-white/5 hover:border-white/10 transition-all">
                        <input type="checkbox" :checked="child.is_completed" @change="fetch('{{ url('/subtasks') }}/'+child.id, { method: 'PUT', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify({ is_completed: $event.target.checked }) })" class="w-4 h-4 border-gray-600 rounded bg-transparent checked:bg-green-500">
                        
                        <div class="flex-1 flex flex-col min-w-0">
                            <input type="text" :value="child.title" @change="fetch('{{ url('/subtasks') }}/'+child.id, { method: 'PUT', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify({ title: $event.target.value }) })" class="bg-transparent border-none text-sm text-gray-300 focus:ring-0 p-0 w-full" :class="child.is_completed ? 'line-through opacity-40' : ''">
                            
                            <div class="flex items-center gap-4 mt-1">
                                @if(Auth::user()->role === 'colaborador')
                                    <div class="flex items-center gap-1.5">
                                        <i class="fas fa-user text-[8px] text-gray-600"></i>
                                        <span class="text-[10px] text-gray-500" x-text="child.team_member ? child.team_member.name : 'Sin asignar'"></span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <i class="far fa-calendar text-[8px] text-gray-600"></i>
                                        <span class="text-[10px] text-gray-500" x-text="child.due_date ? new Date(child.due_date).toLocaleDateString('es-ES') : '--'"></span>
                                    </div>
                                @else
                                    <div class="flex items-center gap-1.5">
                                        <i class="fas fa-user text-[8px] text-gray-600"></i>
                                        <select
                                            @change="fetch('{{ url('/subtasks') }}/'+child.id, { method: 'PUT', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify({ team_member_id: $event.target.value }) }).then(() => window.location.reload())"
                                            class="bg-transparent border-none text-[10px] text-gray-500 focus:ring-0 p-0 cursor-pointer hover:text-orange-500 transition-colors outline-none"
                                        >
                                            <option value="">Sin asignar</option>
                                            @foreach(\App\Models\TeamMember::orderBy('name')->get() as $m)
                                                <option value="{{ $m->id }}" :selected="child.team_member_id == {{ $m->id }}">{{ $m->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <i class="far fa-calendar text-[8px] text-gray-600"></i>
                                        <input type="date" :value="child.due_date ? child.due_date.substring(0, 10) : ''" @change="fetch('{{ url('/subtasks') }}/'+child.id, { method: 'PUT', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify({ due_date: $event.target.value }) }).then(() => window.location.reload())" class="bg-transparent border-none text-[10px] text-gray-500 focus:ring-0 p-0 cursor-pointer hover:text-orange-500 transition-colors">
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        @if(Auth::user()->role !== 'colaborador')
                        <button type="button" @click="if(confirm('¿Eliminar subtarea?')) fetch('{{ url('/subtasks') }}/'+child.id, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } }).then(() => window.location.reload())" class="opacity-0 group-hover:opacity-100 p-2 text-gray-600 hover:text-red-500 transition-all"><i class="fas fa-trash-alt text-[10px]"></i></button>
                        @endif
                    </div>
                </template>
                @if(Auth::user()->role !== 'colaborador')
                <div class="pt-2 flex items-center gap-3 p-3 rounded-xl border border-dashed border-white/10 group focus-within:border-orange-500/50 transition-all">
                    <i class="fas fa-plus text-[10px] text-gray-600 group-focus-within:text-orange-500"></i>
                    <input type="text" x-model="newSubtaskTitle" @keydown.enter.prevent="createChildSubtask()" placeholder="Presiona Enter para agregar una subtarea..." class="flex-1 bg-transparent border-none text-sm text-gray-500 placeholder-gray-700 focus:ring-0 p-0 focus:text-white transition-colors">
                </div>
                @endif
            </div>
        </div>

        <!-- ADJUNTOS -->
        <div class="space-y-4 pt-6 border-t border-white/5">
            <div class="flex justify-between items-center">
                <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Adjuntos <span class="ml-2 text-gray-600" x-text="(currentTask.attachments || []).length"></span></label>
                <label class="cursor-pointer bg-white/5 hover:bg-white/10 p-2 rounded-lg transition-colors">
                    <i class="fas fa-plus text-[10px]"></i>
                    <input type="file" class="hidden" @change="uploadFile($event)">
                </label>
            </div>
            
            <div class="flex flex-wrap gap-4">
                <template x-for="file in (currentTask.attachments || [])" :key="file.id">
                    <div class="group relative w-24 h-24 rounded-xl border border-white/10 bg-white/5 overflow-hidden">
                        <template x-if="file.file_type === 'image'">
                            <img :src="'{{ asset('storage') }}/' + file.file_path" class="w-full h-full object-cover">
                        </template>
                        <template x-if="file.file_type !== 'image'">
                            <div class="w-full h-full flex flex-col items-center justify-center p-2 text-center">
                                <i class="fas fa-file-alt text-2xl text-orange-500 mb-1"></i>
                                <span class="text-[8px] text-gray-400 truncate w-full" x-text="file.file_name"></span>
                            </div>
                        </template>
                        <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                            <a :href="'{{ asset('storage') }}/' + file.file_path" target="_blank" class="text-white hover:text-orange-500"><i class="fas fa-external-link-alt text-xs"></i></a>
                            <button @click="deleteFile(file.id)" class="text-white hover:text-red-500"><i class="fas fa-trash-alt text-xs"></i></button>
                        </div>
                    </div>
                </template>
                
                <div x-show="isUploading" class="w-24 h-24 rounded-xl border border-dashed border-orange-500/50 flex items-center justify-center bg-orange-500/5">
                    <i class="fas fa-spinner animate-spin text-orange-500"></i>
                </div>
            </div>
        </div>

        <!-- COMENTARIOS -->
        <div class="space-y-6 pt-6 border-t border-white/5">
            <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Comentarios</label>
            
            <div class="space-y-4">
                <template x-for="comment in (currentTask.comments || [])" :key="comment.id">
                    <div class="flex gap-3">
                        <div class="w-8 h-8 rounded-full bg-gray-800 border border-white/5 flex items-center justify-center text-[10px] font-bold text-gray-400" x-text="comment.user ? comment.user.name.substring(0, 1) : '?'"></div>
                        <div class="flex-1 bg-white/5 rounded-2xl p-3 border border-white/5">
                            <div class="flex justify-between items-center mb-1.5">
                                <span class="text-[13px] font-bold text-orange-500" x-text="comment.user ? comment.user.name : 'Usuario'"></span>
                                <span class="text-[10px] text-gray-500 font-medium" x-text="new Date(comment.created_at).toLocaleString([], { dateStyle: 'medium', timeStyle: 'short' })"></span>
                            </div>
                            <p class="text-[14px] text-gray-300 leading-[1.6] mb-3" x-text="comment.content"></p>
                            <template x-if="comment.image_path">
                                <div class="rounded-lg overflow-hidden border border-white/5 bg-black/20">
                                    <img :src="'{{ asset('storage') }}/' + comment.image_path" class="max-w-full h-auto cursor-zoom-in" @click="window.open('{{ asset('storage') }}/' + comment.image_path, '_blank')">
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>

            <div class="flex flex-col gap-2 pt-2">
                <template x-if="pastedImage">
                    <div class="relative w-32 h-32 rounded-xl overflow-hidden border border-orange-500/50 self-start ml-11 group">
                        <img :src="pastedImage" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                            <button @click="initCanvas()" class="bg-orange-500 text-black p-2 rounded-lg text-[8px] font-bold uppercase"><i class="fas fa-paint-brush mr-1"></i> Editar</button>
                        </div>
                        <button @click="pastedImage = null" class="absolute top-1 right-1 bg-black/50 text-white w-5 h-5 rounded-full flex items-center justify-center hover:bg-red-500"><i class="fas fa-times text-[8px]"></i></button>
                    </div>
                </template>
                <div class="flex gap-3">
                    <div class="w-8 h-8 rounded-full bg-orange-500 flex items-center justify-center text-[10px] text-black font-black">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div class="flex-1 relative">
                        <textarea
                            x-model="newComment"
                            @keydown.enter.prevent="sendComment()"
                            @paste="handlePaste($event)"
                            placeholder="Escribe un comentario o pega una captura (Ctrl+V)..."
                            class="w-full bg-white/5 border border-white/10 rounded-2xl p-3 text-xs text-gray-300 focus:ring-1 focus:ring-orange-500 outline-none resize-none"
                            rows="2"
                        ></textarea>
                        <div class="absolute bottom-2 right-3 text-[9px] text-gray-600">Enter para enviar | Ctrl+V para capturas</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL DE DIBUJO -->
<div x-show="showDrawingModal"
     class="fixed inset-0 z-[200] flex items-center justify-center bg-black/90 p-4 md:p-8"
     x-transition
     style="display: none;">
    <div class="bg-[#1a1a1a] rounded-2xl border border-white/10 w-full max-w-5xl h-full max-h-[90vh] flex flex-col overflow-hidden shadow-2xl">
        <div class="px-6 py-4 border-b border-white/5 flex justify-between items-center bg-[#222]">
            <div class="flex items-center gap-4">
                <h3 class="text-sm font-bold uppercase tracking-widest text-orange-500">Anotar Captura</h3>
                <div class="flex items-center gap-2 border-l border-white/10 pl-4">
                    <button @click="canvasColor = '#ff0000'" class="w-6 h-6 rounded-full bg-red-600 border-2" :class="canvasColor === '#ff0000' ? 'border-white' : 'border-transparent'"></button>
                    <button @click="canvasColor = '#00ff00'" class="w-6 h-6 rounded-full bg-green-600 border-2" :class="canvasColor === '#00ff00' ? 'border-white' : 'border-transparent'"></button>
                    <button @click="canvasColor = '#ffff00'" class="w-6 h-6 rounded-full bg-yellow-400 border-2" :class="canvasColor === '#ffff00' ? 'border-white' : 'border-transparent'"></button>
                    <button @click="canvasColor = '#0000ff'" class="w-6 h-6 rounded-full bg-blue-600 border-2" :class="canvasColor === '#0000ff' ? 'border-white' : 'border-transparent'"></button>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button @click="clearCanvas()" class="text-[10px] uppercase font-bold text-gray-400 hover:text-white px-3 py-1.5 rounded-lg border border-white/5">Limpiar</button>
                <button @click="saveAnnotatedImage()" class="bg-orange-500 text-black px-4 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-widest hover:bg-orange-600">Listo para enviar</button>
                <button @click="showDrawingModal = false; pastedImage = null" class="text-gray-400 hover:text-white p-2 text-xl">&times;</button>
            </div>
        </div>
        <div class="flex-1 relative overflow-hidden flex items-center justify-center bg-[#0a0a0a]">
            <canvas id="drawingCanvas" @mousedown="startDrawing" @mousemove="draw" @mouseup="stopDrawing" @mouseleave="stopDrawing" @touchstart.prevent="startDrawing" @touchmove.prevent="draw" @touchend.prevent="stopDrawing"></canvas>
        </div>
        <div class="px-6 py-3 bg-[#111] text-[10px] text-gray-500 text-center uppercase tracking-widest">
            Usa el mouse o touch para dibujar instrucciones sobre la imagen
        </div>
    </div>
</div>

<script>
    function projectManager() {
        return {
            openPanel: false, currentTask: {}, newSubtaskTitle: '', newComment: '', isUploading: false, pastedImage: null,
            showDrawingModal: false, canvas: null, ctx: null, isDrawing: false, canvasColor: '#ff0000',
            async openTaskPanel(task, sectionTitle = '', parentTitle = '') {
                // Primero abrimos el panel con los datos que tenemos
                this._fillTaskData(task, sectionTitle, parentTitle);
                this.openPanel = true;

                // Luego refrescamos los datos desde el servidor para asegurar comentarios y adjuntos actualizados
                try {
                    const res = await fetch(`{{ url('/subtasks-detail') }}/${task.id}`, {
                        headers: { 'Accept': 'application/json' }
                    });
                    if (res.ok) {
                        const freshTask = await res.json();
                        this._fillTaskData(freshTask, sectionTitle, parentTitle);
                    }
                } catch (e) {
                    console.error('Error refreshing task data:', e);
                }
            },
            _fillTaskData(task, sectionTitle, parentTitle) {
                let projectName = '';
                if (task.task && task.task.project) projectName = task.task.project.name;
                else if (task.project) projectName = task.project.name;
                else projectName = '{{ $project->name ?? '' }}';

                this.currentTask = {
                    ...task,
                    project_name: projectName,
                    section_title: sectionTitle || (task.task ? task.task.title : 'General'),
                    parent_title: parentTitle || (task.parent ? task.parent.title : ''),
                    team_member_id: task.team_member_id || '',
                    team_member_name: task.team_member ? task.team_member.name : null,
                    due_date: task.due_date ? task.due_date.substring(0, 10) : '',
                    description: task.description || '',
                    attachments: task.attachments || [],
                    comments: (task.comments || []).sort((a, b) => new Date(a.created_at) - new Date(b.created_at))
                };
                this.newSubtaskTitle = ''; this.newComment = ''; this.openPanel = true;
                this.$nextTick(() => { const dateInput = document.querySelector('input[type="date"]'); if (dateInput) dateInput.value = this.currentTask.due_date; });
            },
            getRemainingTime(date) {
                const now = new Date();
                const due = new Date(date);
                const diff = due - now;
                const absDiff = Math.abs(diff);
                const days = Math.floor(absDiff / (1000 * 60 * 60 * 24));
                const hours = Math.floor((absDiff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((absDiff % (1000 * 60 * 60)) / (1000 * 60));
                const timeStr = (days > 0 ? days + 'd ' : '') + hours + 'h ' + minutes + 'm';
                return diff < 0 ? 'VENCIDA HACE ' + timeStr : 'FALTAN ' + timeStr;
            },
            async updateTask() {
                if(!this.currentTask.id) return;
                const body = {
                    title: this.currentTask.title,
                    description: this.currentTask.description,
                    due_date: this.currentTask.due_date,
                    team_member_id: this.currentTask.team_member_id || null,
                    is_completed: !!this.currentTask.is_completed
                };
                return fetch(`{{ url('/subtasks') }}/${this.currentTask.id}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify(body)
                }).then(res => res.json());
            },
            async createChildSubtask() {
                if(!this.newSubtaskTitle || !this.currentTask.id) return;
                const res = await fetch(`{{ url('/subtasks') }}/${this.currentTask.id}/subtasks`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ title: this.newSubtaskTitle })
                });
                const data = await res.json();
                if (!this.currentTask.children) this.currentTask.children = [];
                this.currentTask.children.push(data);
                this.newSubtaskTitle = '';
            },
            async uploadFile(e) {
                const file = e.target.files[0];
                if (!file || !this.currentTask.id) return;
                this.isUploading = true;
                const formData = new FormData();
                formData.append('file', file);
                try {
                    const res = await fetch(`{{ url('/subtasks') }}/${this.currentTask.id}/attachments`, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                        body: formData
                    });
                    const data = await res.json();
                    if (!this.currentTask.attachments) this.currentTask.attachments = [];
                    this.currentTask.attachments.push(data);
                } catch (e) { console.error(e); } finally { this.isUploading = false; e.target.value = ''; }
            },
            async deleteFile(id) {
                if (!confirm('¿Borrar archivo?')) return;
                await fetch(`{{ url('/attachments') }}/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
                this.currentTask.attachments = this.currentTask.attachments.filter(a => a.id !== id);
            },
            handlePaste(e) {
                const items = (e.clipboardData || e.originalEvent.clipboardData).items;
                for (let index in items) {
                    const item = items[index];
                    if (item.kind === 'file' && item.type.includes('image')) {
                        const blob = item.getAsFile();
                        const reader = new FileReader();
                        reader.onload = (event) => { this.pastedImage = event.target.result; this.initCanvas(); };
                        reader.readAsDataURL(blob);
                    }
                }
            },
            initCanvas() {
                this.showDrawingModal = true;
                this.$nextTick(() => {
                    this.canvas = document.getElementById('drawingCanvas');
                    this.ctx = this.canvas.getContext('2d');
                    const img = new Image();
                    img.onload = () => {
                        const maxWidth = window.innerWidth * 0.8;
                        const maxHeight = window.innerHeight * 0.7;
                        let width = img.width, height = img.height;
                        if (width > maxWidth) { height *= maxWidth / width; width = maxWidth; }
                        if (height > maxHeight) { width *= maxHeight / height; height = maxHeight; }
                        this.canvas.width = width; this.canvas.height = height;
                        this.ctx.drawImage(img, 0, 0, width, height);
                        this.ctx.lineJoin = 'round'; this.ctx.lineCap = 'round'; this.ctx.lineWidth = 4;
                    };
                    img.src = this.pastedImage;
                });
            },
            startDrawing(e) {
                this.isDrawing = true;
                const pos = this.getMousePos(e);
                this.ctx.beginPath(); this.ctx.moveTo(pos.x, pos.y);
            },
            draw(e) {
                if (!this.isDrawing) return;
                const pos = this.getMousePos(e);
                this.ctx.strokeStyle = this.canvasColor; this.ctx.lineTo(pos.x, pos.y); this.ctx.stroke();
            },
            stopDrawing() { this.isDrawing = false; },
            getMousePos(e) {
                if (!this.canvas) return {x:0, y:0};
                const rect = this.canvas.getBoundingClientRect();
                const clientX = e.touches ? e.touches[0].clientX : e.clientX;
                const clientY = e.touches ? e.touches[0].clientY : e.clientY;
                return { x: clientX - rect.left, y: clientY - rect.top };
            },
            clearCanvas() { this.initCanvas(); },
            saveAnnotatedImage() { this.pastedImage = this.canvas.toDataURL('image/png'); this.showDrawingModal = false; },
            async sendComment() {
                if (!this.newComment && !this.pastedImage) return;
                try {
                    const res = await fetch(`{{ url('/subtasks') }}/${this.currentTask.id}/comments`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ content: this.newComment, image: this.pastedImage })
                    });
                    const data = await res.json();
                    if (!this.currentTask.comments) this.currentTask.comments = [];
                    this.currentTask.comments.push(data);
                    this.newComment = ''; this.pastedImage = null;
                } catch (e) { console.error(e); }
            }
        }
    }
</script>
