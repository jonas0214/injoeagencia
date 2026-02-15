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
    </style>
</head>
<body class="bg-[#0f1012] text-gray-300 antialiased selection:bg-orange-500 selection:text-white overflow-x-hidden md:overflow-hidden" x-data="{ mobileMenu: false }">
    
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
                            @foreach($projects as $proj)
                            <a href="{{ route('projects.show', $proj) }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-xl hover:bg-white/5 transition-all text-gray-400 hover:text-white">
                                <span class="w-2 h-2 rounded-full bg-orange-500 mr-3 shadow-[0_0_8px_rgba(249,115,22,0.6)]"></span>
                                {{ $proj->name }}
                            </a>
                            @endforeach
                        @endif
                        
                        <a href="{{ route('projects.create') }}" class="group flex items-center px-3 py-2 text-sm font-bold text-orange-500 hover:text-orange-400 transition-colors mt-4">
                            <i class="fas fa-plus w-6 text-center mr-2 bg-orange-500/10 rounded-lg p-1"></i>
                            Nuevo Proyecto
                        </a>
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
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-full bg-gradient-to-br from-orange-500 to-orange-700 flex items-center justify-center text-white font-bold shadow-lg shadow-orange-900/50">
                        {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                    </div>
                    <div>
                        <p class="text-sm font-bold text-white tracking-tight leading-none">{{ Auth::user()->name ?? 'Usuario' }}</p>
                        <p class="text-[10px] text-gray-500 mt-1 uppercase tracking-wider">Admin</p>
                    </div>
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
</body>
</html>