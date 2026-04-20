<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Limbani') }}</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Outfit:wght@200;300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/dark.css">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Outfit', sans-serif; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        .custom-scroll::-webkit-scrollbar { width: 6px; }
        .custom-scroll::-webkit-scrollbar-track { background: transparent; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #333; border-radius: 3px; }
        .custom-scroll::-webkit-scrollbar-thumb:hover { background: #ffaa00; }
        #drawingCanvas { cursor: crosshair; touch-action: none; background-color: #000; }
        
        /* Estilos Flatpickr Personalizados */
        .flatpickr-calendar {
            background: #1a1a1a !important;
            border: 1px solid rgba(255,255,255,0.1) !important;
            box-shadow: 0 10px 25px rgba(0,0,0,0.5) !important;
            border-radius: 12px !important;
        }
        .flatpickr-day.selected {
            background: #f97316 !important;
            border-color: #f97316 !important;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
</head>
<body class="text-gray-800 dark:text-gray-300 antialiased selection:bg-orange-500 selection:text-white overflow-x-hidden md:overflow-hidden transition-colors duration-300"
      x-data="asanaHandler()"
      :class="darkMode ? 'dark' : ''"
      @open-task.window="openTaskPanel($event.detail.task, $event.detail.sectionTitle, $event.detail.parentTitle)">
    
    @stack('scripts')
    
    <!-- CONSTELLATION BACKGROUND CANVAS -->
    <canvas id="appConstellationCanvas" class="fixed top-0 left-0 w-full h-full -z-10 transition-colors duration-500" 
            :class="darkMode ? 'bg-[#0a0a0a]' : 'bg-[#e5e7eb]'">
    </canvas>

    <div class="flex h-screen overflow-hidden relative z-0 bg-transparent">
        
        <!-- Sidebar -->
        <aside
            :class="mobileMenu ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
            class="fixed md:relative w-64 h-full bg-gray-50 dark:bg-[#0a0a0a] border-r border-black/5 dark:border-white/5 text-gray-500 dark:text-gray-400 flex flex-col flex-shrink-0 transition-transform duration-300 ease-in-out z-50 shadow-xl dark:shadow-none">
            
            <div class="h-20 flex items-center justify-between px-8 border-b border-black/5 dark:border-white/5">
                <a href="{{ route('dashboard') }}" class="text-gray-900 dark:text-white font-black text-2xl tracking-tighter hover:text-orange-500 transition-colors">LIMBANI<span class="text-orange-500">.</span></a>
                <button @click="mobileMenu = false" class="md:hidden text-gray-500 hover:text-gray-900 dark:hover:text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-8 overflow-y-auto">
                
                <!-- Toggle Dark Mode -->
                <div class="px-3">
                    <button @click="toggleDarkMode()" class="w-full flex items-center justify-between p-3 rounded-xl bg-gray-100 dark:bg-white/5 text-gray-600 dark:text-gray-400 hover:text-orange-500 transition-all border border-transparent dark:border-white/5 hover:border-orange-500/30">
                        <div class="flex items-center gap-3">
                            <i class="fas" :class="darkMode ? 'fa-moon' : 'fa-sun'"></i>
                            <span class="text-xs font-bold uppercase tracking-widest" x-text="darkMode ? 'Modo Oscuro' : 'Modo Claro'"></span>
                        </div>
                        <div class="w-8 h-4 bg-gray-300 dark:bg-gray-700 rounded-full relative transition-colors">
                            <div class="absolute top-1 left-1 w-2 h-2 rounded-full bg-white transition-transform duration-300" :class="darkMode ? 'translate-x-4 bg-orange-500' : 'translate-x-0 bg-gray-500'"></div>
                        </div>
                    </button>
                </div>

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
                            <a href="{{ route('projects.show', $proj) }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-xl hover:bg-white dark:hover:bg-white/5 transition-all text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
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
                                @if($member->photo)
                                    <img src="{{ asset('storage/' . $member->photo) }}" class="w-8 h-8 rounded-full object-cover border border-white/10">
                                @else
                                    <div class="w-8 h-8 rounded-full bg-gray-800 flex items-center justify-center text-xs font-bold text-white border border-white/10">
                                        {{ substr($member->name, 0, 2) }}
                                    </div>
                                @endif
                                <span class="text-sm font-medium">{{ $member->name }}</span>
                            </div>
                            @endforeach
                        @endif
                </div>
            </nav>

            <div class="border-t border-black/5 dark:border-white/5 p-4 bg-gray-100 dark:bg-white/[0.02]">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        @if(Auth::user()->teamMember && Auth::user()->teamMember->photo)
                            <img src="{{ asset('storage/' . Auth::user()->teamMember->photo) }}" class="h-10 w-10 rounded-full object-cover border-2 border-orange-500 shadow-lg shadow-orange-900/50">
                        @else
                            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-orange-500 to-orange-700 flex items-center justify-center text-white font-bold shadow-lg shadow-orange-900/50">
                                {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                            </div>
                        @endif
                        <div>
                            <p class="text-sm font-bold text-gray-900 dark:text-white tracking-tight leading-none">{{ Auth::user()->name ?? 'Usuario' }}</p>
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
            class="fixed inset-0 bg-black/20 dark:bg-black/60 backdrop-blur-sm z-40 md:hidden"
            style="display: none;">
        </div>

        <main class="flex-1 overflow-y-auto focus:outline-none relative w-full transition-colors duration-300 bg-transparent">
            <!-- Mobile Header -->
            <div class="md:hidden h-16 bg-gray-50 dark:bg-[#0a0a0a] border-b border-black/5 dark:border-white/5 flex items-center justify-between px-6 sticky top-0 z-30">
                <span class="text-gray-900 dark:text-white font-black text-xl tracking-tighter">LIMBANI<span class="text-orange-500">.</span></span>
                <button @click="mobileMenu = true" class="text-gray-400 hover:text-white p-2">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>

            <div class="fixed top-0 left-0 md:left-64 w-full h-full bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-orange-500/10 dark:from-orange-900/20 via-transparent dark:via-[#0f1012] to-transparent dark:to-[#0f1012] pointer-events-none z-0"></div>
            
            <div class="relative z-10">
                @if(session('success'))
                    <div class="m-8 p-4 mb-0 bg-green-100 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300 rounded-xl" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="m-8 p-4 mb-0 bg-red-100 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 rounded-xl" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                        {{ session('error') }}
                    </div>
                @endif
                @if($errors->any())
                    <div class="m-8 p-4 mb-0 bg-red-100 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 rounded-xl">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    @include('components.task-panel-content')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('asanaHandler', () => ({
                mobileMenu: false, openPanel: false, currentTask: {}, newSubtaskTitle: '', newComment: '', isUploading: false, pastedImage: null, showDrawingModal: false, canvas: null, ctx: null, isDrawing: false, canvasColor: '#ff0000',
                darkMode: localStorage.getItem('darkMode') === 'true',
                toggleDarkMode() {
                    this.darkMode = !this.darkMode;
                    localStorage.setItem('darkMode', this.darkMode);
                },
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
                        team_member_photo: task.team_member ? task.team_member.photo : null,
                        ai_suggestion: task.ai_suggestion || null,
                        due_date: task.due_date ? task.due_date.substring(0, 16).replace(' ', 'T') : '',
                        start_date: task.start_date ? task.start_date.substring(0, 16).replace(' ', 'T') : '',
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
                        start_date: this.currentTask.start_date,
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
                },
                deleteSubtask(id) {
                    if (this.currentTask.children) {
                        this.currentTask.children = this.currentTask.children.filter(c => c.id !== id);
                    }
                },

                // Lógica del Canva de Estrellas (Constelación)
                initConstellation() {
                    const bgCanvas = document.getElementById('appConstellationCanvas');
                    if (!bgCanvas) return;
                    const bCtx = bgCanvas.getContext('2d');
                    let w, h;
                    let particles = [];
                    const particleCount = 40; // Estrellas menos densas
                    const connectionDistance = 150;
                    const mouseDistance = 250;
                    let mouse = { x: null, y: null };

                    const resize = () => {
                        w = bgCanvas.width = window.innerWidth;
                        h = bgCanvas.height = window.innerHeight;
                    };

                    window.addEventListener('mousemove', (e) => {
                        mouse.x = e.clientX;
                        mouse.y = e.clientY;
                    });
                    window.addEventListener('resize', resize);

                    class Particle {
                        constructor() {
                            this.x = Math.random() * w;
                            this.y = Math.random() * h;
                            this.vx = (Math.random() - 0.5) * 0.3;
                            this.vy = (Math.random() - 0.5) * 0.3;
                            this.size = Math.random() * 1.5 + 0.5;
                            this.opacity = Math.random() * 0.3 + 0.1;
                        }
                        update() {
                            this.x += this.vx;
                            this.y += this.vy;
                            if (this.x < 0 || this.x > w) this.vx *= -1;
                            if (this.y < 0 || this.y > h) this.vy *= -1;
                        }
                        draw(isDark) {
                            bCtx.fillStyle = isDark ? `rgba(255, 255, 255, ${this.opacity})` : `rgba(0, 0, 0, ${this.opacity})`;
                            bCtx.beginPath();
                            bCtx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                            bCtx.fill();
                        }
                    }

                    resize();
                    for (let i = 0; i < particleCount; i++) {
                        particles.push(new Particle());
                    }

                    const animate = () => {
                        bCtx.clearRect(0, 0, w, h);
                        const isDark = this.darkMode; 

                        particles.forEach((p, index) => {
                            p.update();
                            p.draw(isDark);

                            let dx = mouse.x - p.x;
                            let dy = mouse.y - p.y;
                            let distance = Math.sqrt(dx * dx + dy * dy);

                            if (distance < mouseDistance) {
                                bCtx.strokeStyle = `rgba(249, 115, 22, ${0.4 * (1 - distance / mouseDistance)})`;
                                bCtx.lineWidth = 1;
                                bCtx.beginPath();
                                bCtx.moveTo(p.x, p.y);
                                bCtx.lineTo(mouse.x, mouse.y);
                                bCtx.stroke();
                            }

                            for (let j = index + 1; j < particles.length; j++) {
                                let p2 = particles[j];
                                let dx2 = p.x - p2.x;
                                let dy2 = p.y - p2.y;
                                let dist2 = Math.sqrt(dx2 * dx2 + dy2 * dy2);

                                if (dist2 < connectionDistance) {
                                    bCtx.strokeStyle = isDark ? 
                                        `rgba(255, 255, 255, ${0.05 * (1 - dist2 / connectionDistance)})` : 
                                        `rgba(0, 0, 0, ${0.05 * (1 - dist2 / connectionDistance)})`;
                                    bCtx.lineWidth = 0.5;
                                    bCtx.beginPath();
                                    bCtx.moveTo(p.x, p.y);
                                    bCtx.lineTo(p2.x, p2.y);
                                    bCtx.stroke();
                                }
                            }
                        });
                        requestAnimationFrame(animate);
                    };
                    animate();
                },
                init() {
                    // Start the constellation canvas hook directly upon component load
                    this.initConstellation();

                    // Auto-abrir tarea si viene task_id en la URL
                    const urlParams = new URLSearchParams(window.location.search);
                    const taskId = urlParams.get('task_id');
                    if (taskId) {
                        // Esperar un poco a que todo cargue
                        setTimeout(async () => {
                            await this.openTaskPanel({ id: taskId });
                        }, 500);
                    }
                }
            }));

            // Dashboard Notifications Handler
            Alpine.data('notificationsHandler', () => ({
                notifications: [],
                unreadCount: 0,
                open: false,
                async init() {
                    await this.fetchNotifications();
                    // Polling opcional cada 60 segundos
                    setInterval(() => this.fetchNotifications(), 60000);
                },
                async fetchNotifications() {
                    try {
                        const res = await fetch('{{ route("notifications.index") }}');
                        if (res.ok) {
                            this.notifications = await res.json();
                            this.unreadCount = this.notifications.length;
                        }
                    } catch (e) { console.error('Error fetching notifications:', e); }
                },
                async handleNotificationClick(notif) {
                    try {
                        // Marcar como leída
                        await fetch(`{{ url('/notifications') }}/${notif.id}/read`, {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                        });
                        
                        // Ir al link
                        if (notif.data.link) {
                            window.location.href = notif.data.link;
                        }
                        
                        await this.fetchNotifications();
                    } catch (e) { console.error(e); }
                },
                async markAllAsRead() {
                    try {
                        await fetch(`{{ route("notifications.read-all") }}`, {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                        });
                        await this.fetchNotifications();
                    } catch (e) { console.error(e); }
                },
                formatDate(dateString) {
                    const date = new Date(dateString);
                    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                }
            }));
        });
    </script>
</body>
</html>
