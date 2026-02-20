@extends('layouts.asana')

@section('content')
    <style>
        body {
            background: #0f1012 !important;
            min-height: 100vh;
            color: white;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        /* Forzar que el layout no imponga fondo blanco */
        main, .py-12 { background-color: transparent !important; }
    </style>
    <div class="py-12 px-8 max-w-[1600px] mx-auto" x-data="{ showMemberPanel: false, activeMember: {}, tab: 'team' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Header Minimalista -->
            <div class="mb-8 md:mb-16 flex flex-col md:flex-row justify-between items-start md:items-end border-b border-white/5 pb-8 gap-4">
                <div>
                    <h1 class="text-2xl md:text-4xl font-light text-white tracking-wide mb-1">
                        Gestión de <span class="font-medium text-white">Talento Humano</span>
                    </h1>
                    <p class="text-gray-500 text-[10px] md:text-xs font-medium tracking-[0.15em] uppercase">
                        Administra la información de tus colaboradores, cargos y nómina
                    </p>
                </div>
            </div>

            <!-- Pestañas de Navegación Estilo Asana -->
            <div class="flex items-center gap-4 md:gap-8 border-b border-white/5 mb-10 text-xs md:text-sm font-medium overflow-x-auto scrollbar-hide whitespace-nowrap">
                <a href="{{ route('dashboard') }}" class="pb-4 text-gray-500 hover:text-gray-300 transition-colors">
                    Proyectos
                </a>
                @if(in_array(Auth::user()->role, ['admin', 'ceo', 'rrhh', 'contabilidad']))
                <button @click="tab = 'team'" :class="tab === 'team' ? 'text-white border-b-2 border-orange-500' : 'text-gray-500 hover:text-gray-300'" class="pb-4 transition-all">
                    Equipo & Nómina
                </button>
                <button @click="tab = 'attendance'" :class="tab === 'attendance' ? 'text-white border-b-2 border-orange-500' : 'text-gray-500 hover:text-gray-300'" class="pb-4 transition-all flex items-center gap-2">
                    Asistencia QR <span class="text-[9px] bg-orange-500/20 text-orange-500 px-2 py-0.5 rounded-full uppercase tracking-tighter">Live</span>
                </button>
                @endif
                <a href="#" class="pb-4 text-gray-500 hover:text-gray-300 transition-colors opacity-50 cursor-not-allowed">
                    Informes
                </a>
            </div>

            <div x-show="tab === 'team'" class="bg-white/[0.03] backdrop-blur-md border border-white/10 rounded-2xl p-4 md:p-8 shadow-2xl transition-all duration-300" :class="showMemberPanel ? 'md:mr-[400px]' : ''">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-4">
                    <div>
                        <h3 class="text-xl font-light text-white tracking-wide">Colaboradores Activos</h3>
                    </div>
                    
                    <!-- Botón para abrir modal (usando AlpineJS simple) -->
                    <div x-data="{ open: false }" class="w-full md:w-auto">
                        <button @click="open = true" class="w-full md:w-auto bg-[#1a1a1a] hover:bg-orange-600 text-white font-bold py-3 px-6 rounded-full shadow-lg transition flex items-center justify-center gap-2 text-xs md:text-sm uppercase tracking-widest border border-white/10">
                            <i class="fas fa-user-plus"></i> Nuevo Colaborador
                        </button>

                        <!-- MODAL DE REGISTRO -->
                        <div x-show="open" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="open = false">
                                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                                </div>
                                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                    <form action="{{ route('team.store') }}" method="POST" class="p-6" enctype="multipart/form-data">
                                        @csrf
                                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Registrar Nuevo Talento</h3>
                                        
                                        <div class="grid grid-cols-2 gap-4 mb-4">
                                            <div class="col-span-2">
                                                <label class="block text-sm font-medium text-gray-700">Foto de Perfil</label>
                                                <input type="file" name="photo" accept="image/*" class="mt-1 block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                                            </div>
                                            <div class="col-span-2">
                                                <label class="block text-sm font-medium text-gray-700">Nombre Completo</label>
                                                <input type="text" name="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Cédula / ID</label>
                                                <input type="text" name="cedula" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Teléfono</label>
                                                <input type="text" name="phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Fecha de Nacimiento</label>
                                                <input type="date" name="birth_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Correo Electrónico (Acceso)</label>
                                                <input type="email" name="email" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                            </div>
                                            <div class="col-span-2">
                                                <label class="block text-sm font-medium text-gray-700">Dirección de Residencia</label>
                                                <input type="text" name="address" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                            </div>
                                            <div class="col-span-2">
                                                <label class="block text-sm font-medium text-gray-700">Cargo en la Agencia</label>
                                                <input type="text" name="position" placeholder="Ej: Diseñador Senior, Copywriter..." required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Salario Mensual</label>
                                                <input type="number" step="0.01" name="salary" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                            </div>
                                            <div class="col-span-2">
                                                <label class="block text-sm font-medium text-gray-700">Rol del Sistema</label>
                                                <select name="role" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                                    <option value="colaborador">Colaborador</option>
                                                    <option value="ceo">CEO</option>
                                                    <option value="rrhh">Recursos Humanos</option>
                                                    <option value="contabilidad">Contabilidad</option>
                                                    <option value="admin">Administrador (Soporte)</option>
                                                </select>
                                                <p class="text-[9px] text-gray-500 mt-1 uppercase tracking-tighter">* El rol se aplicará al usuario vinculado por correo electrónico.</p>
                                            </div>
                                            <div class="col-span-2">
                                                <label class="block text-sm font-medium text-gray-700">Información Bancaria</label>
                                                <textarea name="bank_details" rows="2" placeholder="Banco, Tipo de Cuenta, Número..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"></textarea>
                                            </div>
                                        </div>
                                        
                                        <div class="flex justify-end gap-2 mt-6">
                                            <button type="button" @click="open = false" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">Cancelar</button>
                                            <button type="submit" class="bg-orange-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-orange-700">Guardar Ficha</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TABLA DE COLABORADORES -->
                <div class="overflow-x-auto -mx-4 md:mx-0">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b border-white/5">
                                <th scope="col" class="px-4 md:px-6 py-4 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest">Colaborador</th>
                                <th scope="col" class="hidden md:table-cell px-6 py-4 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest">Cargo / Rol</th>
                                <th scope="col" class="hidden lg:table-cell px-6 py-4 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest">Contacto</th>
                                <th scope="col" class="hidden sm:table-cell px-6 py-4 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest">Correo</th>
                                <th scope="col" class="px-4 md:px-6 py-4 text-right text-[10px] font-bold text-gray-500 uppercase tracking-widest">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($team as $member)
                                <tr onclick="window.open('{{ route('team.show', $member) }}', '_blank')" class="hover:bg-white/[0.05] transition-all group cursor-pointer">
                                    <td class="px-6 py-6 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 border border-white/10 rounded-full overflow-hidden group-hover:scale-110 transition-transform bg-white/5">
                                                @if($member->photo)
                                                    <img src="{{ asset('storage/' . $member->photo) }}" alt="" class="w-full h-full object-cover">
                                                @else
                                                    <div class="w-full h-full bg-orange-500/10 flex items-center justify-center text-orange-500 font-bold text-xs uppercase">
                                                        {{ substr($member->name, 0, 2) }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-white group-hover:text-orange-500 transition-colors">{{ $member->name }}</div>
                                                <div class="text-[9px] text-gray-500 font-bold uppercase tracking-widest">ID: {{ $member->cedula }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="hidden md:table-cell px-6 py-6 whitespace-nowrap">
                                        <div class="flex flex-col gap-1">
                                            <span class="px-3 py-1 inline-flex text-[10px] leading-5 font-bold rounded-full bg-white/5 border border-white/10 text-gray-400 uppercase tracking-widest group-hover:border-orange-500/30 transition-colors w-fit">
                                                {{ $member->position }}
                                            </span>
                                            @if($member->user)
                                                <span class="text-[9px] font-black text-orange-500 uppercase tracking-tighter ml-1">
                                                    <i class="fas fa-shield-alt mr-1"></i> {{ strtoupper($member->user->role) }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="hidden lg:table-cell px-6 py-6 whitespace-nowrap">
                                        <div class="text-xs text-gray-400 font-medium"><i class="fas fa-phone text-gray-600 mr-2"></i> {{ $member->phone }}</div>
                                    </td>
                                    <td class="hidden sm:table-cell px-6 py-6 whitespace-nowrap">
                                        <div class="text-xs text-gray-500 italic">{{ $member->email ?? 'no-email@agency.com' }}</div>
                                    </td>
                                    <td class="px-4 md:px-6 py-6 whitespace-nowrap text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <div class="w-1.5 h-1.5 rounded-full {{ $member->status == 'active' ? 'bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.5)]' : 'bg-red-500' }}"></div>
                                            <span class="text-[10px] font-bold uppercase tracking-widest {{ $member->status == 'active' ? 'text-green-500' : 'text-red-500' }}">
                                                {{ $member->status == 'active' ? 'Activo' : 'Inactivo' }}
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-gray-500 italic">No hay colaboradores registrados aún.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- CONTENIDO DE ASISTENCIA (Integrado con AlpineJS) -->
            <div x-show="tab === 'attendance'" style="display: none;" class="bg-white/[0.03] backdrop-blur-md border border-white/10 rounded-2xl p-4 md:p-8 shadow-2xl">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-6">
                    <div>
                        <h3 class="text-xl font-light text-white tracking-wide">Registro de Asistencia</h3>
                        <p class="text-[10px] text-gray-500 uppercase tracking-widest mt-1">Control de entrada y salida mediante QR</p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                        <a href="{{ route('attendance.scanner') }}" target="_blank" class="w-full sm:w-auto justify-center bg-white text-black font-bold py-3 px-6 rounded-xl shadow-lg transition flex items-center gap-2 text-xs uppercase tracking-widest hover:bg-gray-200">
                            <i class="fas fa-qrcode"></i> Abrir Scanner
                        </a>
                        <a href="{{ route('attendance.index') }}" class="w-full sm:w-auto justify-center bg-white/5 border border-white/10 text-white font-bold py-3 px-6 rounded-xl transition flex items-center gap-2 text-xs uppercase tracking-widest hover:bg-white/10">
                            <i class="fas fa-external-link-alt"></i> Ver Historial
                        </a>
                    </div>
                </div>

                <div class="overflow-x-auto -mx-4 md:mx-0">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b border-white/5">
                                <th class="px-4 md:px-6 py-4 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest">Colaborador</th>
                                <th class="px-4 md:px-6 py-4 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest">Entrada</th>
                                <th class="hidden sm:table-cell px-6 py-4 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest">Salida</th>
                                <th class="px-4 md:px-6 py-4 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest">Total Hoy</th>
                                <th class="px-4 md:px-6 py-4 text-right text-[10px] font-bold text-gray-500 uppercase tracking-widest">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @php
                                $todayAttendances = \App\Models\Attendance::with('teamMember')->whereDate('check_in', \Carbon\Carbon::today())->latest()->get();
                                // Agrupar para sumar tiempos por persona hoy
                                $dailyTotals = [];
                                foreach($todayAttendances as $att) {
                                    if($att->check_out) {
                                        $mins = $att->check_in->diffInMinutes($att->check_out);
                                        $dailyTotals[$att->team_member_id] = ($dailyTotals[$att->team_member_id] ?? 0) + $mins;
                                    }
                                }
                            @endphp
                            @forelse($todayAttendances as $record)
                                <tr class="hover:bg-white/[0.02] transition-colors">
                                    <td class="px-4 md:px-6 py-5 whitespace-nowrap">
                                        <div class="flex items-center gap-3 md:gap-4">
                                            @if($record->teamMember && $record->teamMember->photo)
                                                <img src="{{ asset('storage/' . $record->teamMember->photo) }}" class="w-8 h-8 rounded-lg object-cover">
                                            @else
                                                <div class="w-8 h-8 rounded-lg bg-orange-500/10 flex items-center justify-center text-orange-500 font-bold text-[10px]">
                                                    {{ substr($record->teamMember->name, 0, 1) }}
                                                </div>
                                            @endif
                                            <p class="text-xs md:text-sm font-medium text-white">{{ $record->teamMember->name }}</p>
                                        </div>
                                    </td>
                                    <td class="px-4 md:px-6 py-5 whitespace-nowrap">
                                        <span class="text-xs md:text-sm text-gray-300 font-mono">{{ $record->check_in->format('H:i:s') }}</span>
                                    </td>
                                    <td class="hidden sm:table-cell px-6 py-5 whitespace-nowrap">
                                        @if($record->check_out)
                                            <span class="text-sm text-gray-300 font-mono">{{ $record->check_out->format('H:i:s') }}</span>
                                        @else
                                            <span class="text-[10px] font-bold text-green-500/50 uppercase tracking-widest italic animate-pulse">Presente</span>
                                        @endif
                                    </td>
                                    <td class="px-4 md:px-6 py-5 whitespace-nowrap">
                                        @php
                                            $totalMins = $dailyTotals[$record->team_member_id] ?? 0;
                                            $hours = floor($totalMins / 60);
                                            $mins = $totalMins % 60;
                                        @endphp
                                        <span class="text-xs font-black text-orange-500">{{ $hours }}h {{ $mins }}m</span>
                                    </td>
                                    <td class="px-4 md:px-6 py-5 whitespace-nowrap text-right">
                                        @if($record->status == 'late')
                                            <span class="px-2 py-0.5 rounded bg-red-500/10 text-red-500 text-[9px] font-black uppercase tracking-widest border border-red-500/20">Tardanza</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded bg-green-500/10 text-green-500 text-[9px] font-black uppercase tracking-widest border border-green-500/20">A tiempo</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-10 text-center text-gray-500 italic text-sm">No hay registros de asistencia para hoy.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- PANEL LATERAL DE DETALLE (Asana Style) -->
    <div x-show="showMemberPanel"
         x-transition:enter="transform transition ease-in-out duration-300"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transform transition ease-in-out duration-300"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         class="fixed inset-y-0 right-0 w-full md:w-[400px] bg-[#1a1a1a]/95 backdrop-blur-xl border-l border-white/10 shadow-2xl z-[60] flex flex-col"
         style="display: none;">
        
        <!-- Header Panel -->
        <div class="px-8 py-6 border-b border-white/5 flex justify-between items-center bg-[#1a1a1a]">
            <h2 class="text-sm font-bold text-gray-500 uppercase tracking-[0.2em]">Detalle del Perfil</h2>
            <button @click="showMemberPanel = false" class="text-gray-500 hover:text-white transition-colors">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto custom-scroll p-8 space-y-10">
            <!-- Avatar & Nombre -->
            <div class="flex flex-col items-center text-center space-y-4">
                <div class="w-24 h-24 rounded-3xl border border-white/10 overflow-hidden shadow-2xl shadow-orange-500/10 bg-white/5">
                    <template x-if="activeMember.photo">
                        <img :src="'/storage/' + activeMember.photo" class="w-full h-full object-cover">
                    </template>
                    <template x-if="!activeMember.photo">
                        <div class="w-full h-full bg-orange-500/10 flex items-center justify-center text-orange-500 text-3xl font-bold uppercase">
                            <span x-text="activeMember.name ? activeMember.name.substring(0, 2).toUpperCase() : ''"></span>
                        </div>
                    </template>
                </div>
                <div>
                    <h3 class="text-2xl font-medium text-white" x-text="activeMember.name"></h3>
                    <p class="text-orange-500 text-xs font-bold uppercase tracking-widest mt-1" x-text="activeMember.position"></p>
                </div>
            </div>

            <!-- Información General -->
            <div class="space-y-6">
                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-gray-600 uppercase tracking-widest">Información de Contacto</label>
                    <div class="grid grid-cols-1 gap-3">
                        <div class="bg-white/5 rounded-xl p-4 border border-white/5">
                            <p class="text-[10px] text-gray-500 uppercase font-bold mb-1">Correo Electrónico</p>
                            <p class="text-sm text-gray-200" x-text="activeMember.email || 'No registrado'"></p>
                        </div>
                        <div class="bg-white/5 rounded-xl p-4 border border-white/5">
                            <p class="text-[10px] text-gray-500 uppercase font-bold mb-1">Teléfono Móvil</p>
                            <p class="text-sm text-gray-200" x-text="activeMember.phone"></p>
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-gray-600 uppercase tracking-widest">Datos Administrativos</label>
                    <div class="grid grid-cols-1 gap-3">
                        <div class="bg-white/5 rounded-xl p-4 border border-white/5 flex justify-between items-center">
                            <div>
                                <p class="text-[10px] text-gray-500 uppercase font-bold mb-1">Identificación</p>
                                <p class="text-sm text-gray-200" x-text="activeMember.cedula"></p>
                            </div>
                            <div class="px-2 py-1 rounded bg-white/10 text-[9px] font-bold text-gray-400">CC</div>
                        </div>
                        <div class="bg-white/5 rounded-xl p-4 border border-white/5 flex justify-between items-center">
                            <div>
                                <p class="text-[10px] text-gray-500 uppercase font-bold mb-1">Asignación Salarial</p>
                                <p class="text-lg font-medium text-white">
                                    <span class="text-orange-500 mr-1">$</span>
                                    <span x-text="new Intl.NumberFormat('es-CO').format(activeMember.salary)"></span>
                                </p>
                            </div>
                            <i class="fas fa-money-bill-wave text-gray-700"></i>
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-gray-600 uppercase tracking-widest">Tesorería & Pagos</label>
                    <div class="bg-white/5 rounded-xl p-4 border border-white/5">
                        <p class="text-[10px] text-gray-500 uppercase font-bold mb-1">Detalles Bancarios</p>
                        <p class="text-xs text-gray-300 leading-relaxed italic" x-text="activeMember.bank_details || 'Información pendiente de registro'"></p>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="pt-6 border-t border-white/5 grid grid-cols-2 gap-4">
                <button class="py-3 rounded-xl bg-white/5 border border-white/10 text-xs font-bold text-gray-400 hover:bg-white/10 transition-colors uppercase tracking-widest">
                    Editar Ficha
                </button>
                <button class="py-3 rounded-xl bg-red-500/10 border border-red-500/20 text-xs font-bold text-red-500 hover:bg-red-500/20 transition-colors uppercase tracking-widest">
                    Dar de Baja
                </button>
            </div>
        </div>
    </div>
@endsection