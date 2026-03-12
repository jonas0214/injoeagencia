<div x-data="notificationsHandler()" x-init="init()" class="relative">
    <!-- Notification Button with Label -->
    <button @click="open = !open" 
            class="relative flex items-center gap-3 px-6 py-2.5 rounded-full bg-white dark:bg-white/10 border transition-all duration-500 group overflow-hidden"
            :class="unreadCount > 0 
                ? 'border-orange-500/50 shadow-[0_0_20px_rgba(249,115,22,0.3)] dark:shadow-[0_0_30px_rgba(249,115,22,0.5)] bg-orange-500/5' 
                : 'border-gray-200 dark:border-white/10 hover:border-orange-500/30'">
        
        <!-- Persistent Blinking Glow when unread -->
        <div x-show="unreadCount > 0" 
             class="absolute inset-0 bg-orange-500/10 animate-[pulse_2s_infinite] pointer-events-none"></div>
        
        <div class="relative flex items-center justify-center">
            <i class="fas fa-bell text-sm transition-transform duration-500 group-hover:scale-110" 
               :class="unreadCount > 0 ? 'text-orange-500 animate-[bounce_3s_infinite]' : 'text-gray-400 dark:text-gray-500'"></i>
        </div>

        <span class="relative text-[10px] font-bold uppercase tracking-[0.2em]"
              :class="unreadCount > 0 ? 'text-orange-600 dark:text-orange-400' : 'text-gray-500 dark:text-gray-400'">
            Notificaciones
        </span>
        
        <!-- Unread Badge -->
        <span x-show="unreadCount > 0" 
              class="relative flex h-5 w-5 items-center justify-center rounded-full bg-orange-600 text-[10px] font-black text-white border-2 border-white dark:border-[#0a0a0a] shadow-lg"
              x-text="unreadCount"></span>
    </button>

    <!-- Notifications Dropdown Panel -->
    <div x-show="open" 
         @click.away="open = false"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         class="absolute right-0 mt-4 w-80 md:w-96 bg-white dark:bg-[#111111]/95 backdrop-blur-3xl border border-gray-200 dark:border-white/10 rounded-[2rem] shadow-[0_30px_60px_rgba(0,0,0,0.3)] z-[100] overflow-hidden"
         style="display: none;">
        
        <div class="p-6 border-b border-gray-100 dark:border-white/5 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-gray-800 dark:text-white uppercase tracking-widest">Centro de Control</h3>
                <p class="text-[10px] text-gray-500 mt-0.5">Alertas en tiempo real</p>
            </div>
            <button @click="markAllAsRead()" x-show="unreadCount > 0" class="text-[10px] font-bold text-orange-500 hover:text-orange-400 transition-colors uppercase tracking-widest">
                Marcar todo
            </button>
        </div>

        <div class="max-h-[400px] overflow-y-auto custom-scroll p-4 space-y-3">
            <template x-for="notif in notifications" :key="notif.id">
                <div @click="handleNotificationClick(notif)" 
                     class="p-4 rounded-2xl bg-gray-50 dark:bg-white/5 border border-transparent hover:border-orange-500/30 hover:bg-white dark:hover:bg-white/10 cursor-pointer transition-all group">
                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-xl bg-orange-500/10 flex items-center justify-center text-orange-500 text-sm shrink-0 shadow-inner">
                            <i class="fas" :class="notif.data.type === 'task_assigned' ? 'fa-tasks shadow-[0_0_10px_rgba(249,115,22,0.3)]' : 'fa-comment-dots'"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex justify-between items-start mb-1">
                                <p class="text-xs font-bold text-gray-900 dark:text-white truncate pr-2" x-text="notif.data.title"></p>
                                <span class="text-[8px] text-gray-400 uppercase font-medium whitespace-nowrap" x-text="formatDate(notif.created_at)"></span>
                            </div>
                            <p class="text-[11px] text-gray-500 dark:text-gray-400 leading-tight" x-text="notif.data.message"></p>
                            
                            <!-- Action button hint -->
                            <div class="mt-2 flex items-center gap-1 text-orange-500 opacity-0 group-hover:opacity-100 transition-opacity">
                                <span class="text-[9px] font-bold uppercase tracking-widest">Ver Detalles</span>
                                <i class="fas fa-arrow-right text-[8px]"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <div x-show="notifications.length === 0" class="py-16 text-center">
                <div class="w-20 h-20 rounded-full bg-gray-50 dark:bg-white/5 flex items-center justify-center mx-auto mb-4 border border-dashed border-gray-200 dark:border-white/10">
                    <i class="fas fa-bell-slash text-gray-300 dark:text-gray-800 text-2xl"></i>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-500 font-medium">Todo bajo control, Manada. 🐵</p>
                <p class="text-[10px] text-gray-400 mt-1 uppercase tracking-widest">No hay nuevas alertas</p>
            </div>
        </div>
        
        <div class="p-4 bg-gray-50 dark:bg-white/[0.02] border-t border-gray-100 dark:border-white/5 text-center">
            <p class="text-[9px] text-gray-400 font-medium uppercase tracking-[0.2em]">Limbani Intelligent Control Console</p>
        </div>
    </div>
</div>
