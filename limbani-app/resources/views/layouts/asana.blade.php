<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Limbani') }}</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Outfit:wght@200;300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js Plugins -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <!-- Alpine.js Core -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Outfit', sans-serif; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        .custom-scroll::-webkit-scrollbar { width: 6px; }
        .custom-scroll::-webkit-scrollbar-track { background: transparent; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #333; border-radius: 3px; }
        .custom-scroll::-webkit-scrollbar-thumb:hover { background: #ffaa00; }
        #drawingCanvas { cursor: crosshair; touch-action: none; background-color: #000; }
    </style>
</head>
<body class="bg-[#0f1012] text-gray-300 antialiased selection:bg-orange-500 selection:text-white overflow-x-hidden md:overflow-hidden"
      x-data="asanaHandler()"
      @open-task.window="openTaskPanel($event.detail.task, $event.detail.sectionTitle, $event.detail.parentTitle)">
    
    @stack('scripts')
    <div class="flex h-screen overflow-hidden relative">
        
        <!-- Sidebar -->
        <aside
            :class="mobileMenu ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
            class="fixed md:relative w-64 h-full bg-[#0a0a0a] border-r border-white/5 text-gray-400 flex flex-col flex-shrink-0 transition-transform duration-300 ease-in-out z-50">
            
            <div class="h-20 flex items-center justify-between px-8 border-b border-white/5">
                <span class="text-white font-black text-2xl tracking-tighter">LIMBANI<span class="text-orange-500">.</span></span>
                <button @click="mobileMenu = false" class="md:hidden text-gray-500 hover:text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-8 overflow-y-auto">
                
                <div>
                    <h3 class="px-3 text-[10px] font-bold text-gray-600 uppercase tracking-widest mb-4">Proyectos</h3>
                    <div class="space-y-1">
                        @if(isset($projects))
                            @php
                                $sidebarProjects = $projects;
                                if(Auth::user()->role === 'colaborador') {
                                    $teamMemberId = Auth::user()->teamMember ? Auth::user()->teamMember->id : null;
                                    $sidebarProjects = $projects->filter(function($p) use ($teamMemberId) {
                                        return $p->tasks->flatMap->subtasks->where('team_member_id', $teamMemberId)->count() > 0;
                                    });
                                }
                            @endphp
                            @foreach($sidebarProjects as $proj)
                            <a href="{{ route('projects.show', $proj) }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-xl hover:bg-white/5 transition-all text-gray-400 hover:text-white">
                                <span class="w-2 h-2 rounded-full bg-orange-500 mr-3 shadow-[0_0_8px_rgba(249,115,22,0.6)]"></span>
                                {{ $proj->name }}
                            </a>
                            @endforeach
                        @endif
                        
                        @if(in_array(Auth::user()->role, ['admin', 'ceo']))
                        <a href="{{ route('projects.create') }}" class="group flex items-center px-3 py-2 text-sm font-bold text-orange-500 hover:text-orange-400 transition-colors mt-4">
                            <i class="fas fa-plus w-6 text-center mr-2 bg-orange-500/10 rounded-lg p-1"></i>
                            Nuevo Proyecto
                        </a>
                        @endif
                    </div>
                </div>

                <div>
                    <h3 class="px-3 text-[10px] font-bold text-gray-600 uppercase tracking-widest mb-4">Equipo</h3>
                    <div class="space-y-3 px-3">
                        @if(isset($team))
                            @foreach($team as $member)
                            <div class="flex items-center gap-3 opacity-60 hover:opacity-100 transition-opacity cursor-pointer">
                                <div class="w-8 h-8 rounded-full bg-gray-800 flex items-center justify-center text-xs font-bold text-white border border-white/10">
                                    {{ substr($member->name, 0, 2) }}
                                </div>
                                <span class="text-sm font-medium">{{ $member->name }}</span>
                            </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </nav>

            <div class="border-t border-white/5 p-4 bg-white/[0.02]">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-orange-500 to-orange-700 flex items-center justify-center text-white font-bold shadow-lg shadow-orange-900/50">
                            {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                        </div>
                        <div>
                            <p class="text-sm font-bold text-white tracking-tight leading-none">{{ Auth::user()->name ?? 'Usuario' }}</p>
                            <p class="text-[10px] text-gray-500 mt-1 uppercase tracking-wider">{{ strtoupper(Auth::user()->role ?? 'Invitado') }}</p>
                        </div>
                    </div>
                    
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-gray-500 hover:text-red-500 transition-colors p-2" title="Cerrar Sesión">
                            <i class="fas fa-power-off"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Overlay for mobile -->
        <div
            x-show="mobileMenu"
            @click="mobileMenu = false"
            x-transition:enter="transition opacity-ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition opacity-ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40 md:hidden"
            style="display: none;">
        </div>

        <main class="flex-1 overflow-y-auto bg-[#0f1012] focus:outline-none relative w-full">
            <!-- Mobile Header -->
            <div class="md:hidden h-16 bg-[#0a0a0a] border-b border-white/5 flex items-center justify-between px-6 sticky top-0 z-30">
                <span class="text-white font-black text-xl tracking-tighter">LIMBANI<span class="text-orange-500">.</span></span>
                <button @click="mobileMenu = true" class="text-gray-400 hover:text-white p-2">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>

            <div class="fixed top-0 left-0 md:left-64 w-full h-full bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-orange-900/20 via-[#0f1012] to-[#0f1012] pointer-events-none z-0"></div>
            
            <div class="relative z-10">
                @yield('content')
            </div>
        </main>
    </div>

    @include('components.task-panel-content')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('asanaHandler', () => ({
                mobileMenu: false, openPanel: false, currentTask: {}, newSubtaskTitle: '', newComment: '', isUploading: false, pastedImage: null, showDrawingModal: false, canvas: null, ctx: null, isDrawing: false, canvasColor: '#ff0000',
                async openTaskPanel(task, sectionTitle = '', parentTitle = '') {
                    this._fillTaskData(task, sectionTitle, parentTitle);
                    this.openPanel = true;
                    try {
                        const res = await fetch(`{{ url('/subtasks-detail') }}/${task.id}`, { headers: { 'Accept': 'application/json' } });
                        if (res.ok) {
                            const freshTask = await res.json();
                            this._fillTaskData(freshTask, sectionTitle, parentTitle);
                        }
                    } catch (e) { console.error('Error refreshing task data:', e); }
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
                        due_date: task.due_date ? task.due_date.substring(0, 16).replace(' ', 'T') : '',
                        description: task.description || '',
                        attachments: task.attachments || [],
                        comments: (task.comments || []).sort((a, b) => new Date(a.created_at) - new Date(b.created_at))
                    };
                    this.newSubtaskTitle = ''; this.newComment = '';
                    this.$nextTick(() => { const dateInput = document.querySelector('input[type=datetime-local]'); if (dateInput) dateInput.value = this.currentTask.due_date; });
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
                        is_completed: !!this.currentTask.is_completed,
                        is_approved: !!this.currentTask.is_approved
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
            }));
        });
    </script>
</body>
</html>
