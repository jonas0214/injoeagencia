@extends('layouts.asana')

@section('content')
<style>
    body {
        background: #0f1012 !important;
        min-height: 100vh;
        color: white;
    }
    .profile-card {
        background: rgba(255, 255, 255, 0.03);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.08);
    }
    .data-box {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.05);
        transition: all 0.3s ease;
    }
    .data-box:hover {
        background: rgba(255, 255, 255, 0.05);
        border-color: rgba(255, 170, 0, 0.2);
    }
</style>

<div class="py-12 px-8 max-w-[1200px] mx-auto">
    
    <!-- Header Navegación -->
    <div class="flex items-center gap-4 mb-12">
        <a href="{{ route('team.index') }}" class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-gray-400 hover:text-white transition-colors">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h2 class="text-sm font-bold text-gray-500 uppercase tracking-[0.2em]">Perfil del Colaborador</h2>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Lateral Izquierdo: Info Principal -->
        <div class="lg:col-span-4 space-y-6">
            <div class="profile-card rounded-3xl p-10 flex flex-col items-center text-center shadow-2xl">
                <div class="w-32 h-32 rounded-[2.5rem] border border-white/10 overflow-hidden mb-6 shadow-2xl shadow-orange-500/5">
                    @if($teamMember->photo)
                        <img src="{{ asset('storage/' . $teamMember->photo) }}" alt="" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-orange-500/10 flex items-center justify-center text-orange-500 text-4xl font-bold uppercase">
                            {{ substr($teamMember->name, 0, 2) }}
                        </div>
                    @endif
                </div>
                <h1 class="text-2xl font-medium text-white mb-1">{{ $teamMember->name }}</h1>
                <p class="text-orange-500 text-xs font-bold uppercase tracking-widest">{{ $teamMember->position }}</p>
                
                <div class="mt-8 pt-8 border-t border-white/5 w-full space-y-4">
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-gray-500 font-bold uppercase tracking-widest">Estado</span>
                        <span class="flex items-center gap-2 text-green-500 font-bold uppercase">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Activo
                        </span>
                    </div>
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-gray-500 font-bold uppercase tracking-widest">Ingreso</span>
                        <span class="text-gray-300">{{ $teamMember->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>

            <div class="profile-card rounded-3xl p-6 flex flex-col gap-3">
                <a href="{{ route('team.edit', $teamMember) }}" class="w-full py-4 rounded-xl bg-white text-black font-bold text-xs text-center uppercase tracking-widest hover:bg-gray-200 transition-colors">
                    Editar Información
                </a>
                
                <form action="{{ route('team.destroy', $teamMember) }}" method="POST" onsubmit="return confirm('¿Estás seguro de dar de baja a este colaborador? Esta acción eliminará sus datos permanentemente.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full py-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-500 font-bold text-xs uppercase tracking-widest hover:bg-red-500/20 transition-colors">
                        Dar de Baja
                    </button>
                </form>
            </div>
        </div>

        <!-- Columna Derecha: Datos Detallados -->
        <div class="lg:col-span-8 space-y-8">
            
            <!-- Bloque Personal -->
            <div class="profile-card rounded-3xl p-8">
                <h3 class="text-sm font-bold text-white uppercase tracking-widest mb-8 flex items-center gap-3">
                    <i class="fas fa-user-circle text-orange-500/50"></i> Información Personal
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="data-box p-5 rounded-2xl">
                        <p class="text-[10px] text-gray-600 font-bold uppercase tracking-widest mb-1">Cédula / Identificación</p>
                        <p class="text-sm text-gray-200">{{ $teamMember->cedula }}</p>
                    </div>
                    <div class="data-box p-5 rounded-2xl">
                        <p class="text-[10px] text-gray-600 font-bold uppercase tracking-widest mb-1">Correo Electrónico</p>
                        <p class="text-sm text-gray-200">{{ $teamMember->email ?? 'No registrado' }}</p>
                    </div>
                    <div class="data-box p-5 rounded-2xl">
                        <p class="text-[10px] text-gray-600 font-bold uppercase tracking-widest mb-1">Teléfono de Contacto</p>
                        <p class="text-sm text-gray-200">{{ $teamMember->phone }}</p>
                    </div>
                    <div class="data-box p-5 rounded-2xl">
                        <p class="text-[10px] text-gray-600 font-bold uppercase tracking-widest mb-1">Cargo Actual</p>
                        <p class="text-sm text-gray-200">{{ $teamMember->position }}</p>
                    </div>
                    <div class="data-box p-5 rounded-2xl">
                        <p class="text-[10px] text-gray-600 font-bold uppercase tracking-widest mb-1">Rol de Sistema</p>
                        <p class="text-sm text-orange-500 font-black uppercase tracking-tighter">
                            <i class="fas fa-shield-alt mr-1"></i> {{ $teamMember->user->role ?? 'Sin usuario vinculado' }}
                        </p>
                    </div>
                    <div class="data-box p-5 rounded-2xl">
                        <p class="text-[10px] text-gray-600 font-bold uppercase tracking-widest mb-1">Fecha de Nacimiento</p>
                        <p class="text-sm text-gray-200">{{ $teamMember->birth_date ? $teamMember->birth_date->format('d/m/Y') : 'No registrada' }}</p>
                    </div>
                    <div class="data-box p-5 rounded-2xl md:col-span-2">
                        <p class="text-[10px] text-gray-600 font-bold uppercase tracking-widest mb-1">Dirección de Residencia</p>
                        <p class="text-sm text-gray-200">{{ $teamMember->address ?? 'No registrada' }}</p>
                    </div>
                </div>
            </div>

            <!-- Bloque Financiero -->
            <div class="profile-card rounded-3xl p-8 border-orange-500/20 shadow-orange-500/5 shadow-2xl">
                <h3 class="text-sm font-bold text-white uppercase tracking-widest mb-8 flex items-center gap-3">
                    <i class="fas fa-wallet text-orange-500/50"></i> Nómina & Tesorería
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="data-box p-6 rounded-2xl bg-orange-500/[0.03] border-orange-500/10">
                        <p class="text-[10px] text-gray-600 font-bold uppercase tracking-widest mb-2">Asignación Mensual</p>
                        <p class="text-3xl font-medium text-white">
                            <span class="text-orange-500 mr-2">$</span>{{ number_format($teamMember->salary, 2) }}
                        </p>
                    </div>
                    <div class="data-box p-6 rounded-2xl">
                        <p class="text-[10px] text-gray-600 font-bold uppercase tracking-widest mb-2">Información Bancaria</p>
                        <p class="text-sm text-gray-300 leading-relaxed">{{ $teamMember->bank_details ?? 'Pendiente de asignar datos de pago.' }}</p>
                    </div>
                </div>
            </div>

            <!-- CARNET DE LA AGENCIA (Imprimible) -->
            <div class="mt-12 pt-12 border-t border-white/5">
                <h3 class="text-sm font-bold text-gray-500 uppercase tracking-[0.2em] mb-8 text-center">Identificación Corporativa (Carnet)</h3>
                
                <div class="flex justify-center">
                    <!-- Carnet Físico -->
                    <div id="agency-card" class="w-[350px] h-[550px] bg-gradient-to-br from-[#1a1a1a] to-[#0a0a0a] rounded-[2.5rem] border border-white/10 shadow-2xl relative overflow-hidden flex flex-col items-center p-8 text-center">
                        <!-- Decoración Background -->
                        <div class="absolute top-[-50px] right-[-50px] w-48 h-48 bg-orange-500/10 rounded-full blur-[60px]"></div>
                        <div class="absolute bottom-[-50px] left-[-50px] w-48 h-48 bg-orange-500/5 rounded-full blur-[60px]"></div>
                        
                        <!-- Logo Injoe -->
                        <div class="mb-10 relative z-10">
                            <span class="text-2xl font-black tracking-tighter text-white">INJOE<span class="text-orange-500">.</span></span>
                            <p class="text-[8px] font-bold text-gray-500 tracking-[0.3em] uppercase mt-1">Future Agency</p>
                        </div>

                        <!-- Foto del Colaborador -->
                        <div class="w-36 h-36 rounded-[3rem] border-2 border-orange-500/20 p-1.5 mb-6 relative z-10 shadow-2xl">
                            <div class="w-full h-full rounded-[2.8rem] overflow-hidden bg-white/5 border border-white/10">
                                @if($teamMember->photo)
                                    <img src="{{ asset('storage/' . $teamMember->photo) }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-orange-500 text-3xl font-bold">
                                        {{ substr($teamMember->name, 0, 2) }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Info Colaborador -->
                        <div class="relative z-10 mb-4">
                            <h2 class="text-xl font-medium text-white mb-1 uppercase tracking-tight">{{ $teamMember->name }}</h2>
                            <p class="text-orange-500 text-[10px] font-bold uppercase tracking-[0.2em]">{{ $teamMember->position }}</p>
                        </div>

                        <!-- Datos Adicionales -->
                        <div class="relative z-10 mb-6">
                            <p class="text-[10px] font-medium text-gray-400 uppercase tracking-[0.2em]">Cédula: <span class="text-white">{{ $teamMember->cedula }}</span></p>
                        </div>

                        <!-- Separador -->
                        <div class="w-12 h-0.5 bg-orange-500/20 rounded-full mb-6 relative z-10"></div>

                        <!-- QR Code (JS QR Generator para asegurar visualización offline/local) -->
                        <div class="bg-white p-3 rounded-2xl shadow-xl relative z-10 mb-6">
                            <div id="qrcode-container" class="w-24 h-24"></div>
                        </div>

                        <!-- Footer Carnet -->
                        <div class="mt-auto relative z-10">
                            <div class="px-4 py-2 rounded-full bg-orange-500/10 border border-orange-500/20 text-[9px] font-bold text-orange-500 uppercase tracking-[0.2em]">
                                Colaborador Autorizado
                            </div>
                            <p class="text-[7px] font-medium text-gray-600 uppercase tracking-widest mt-4">Válido para el año 2026</p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-center mt-10">
                    <button id="download-btn" class="flex items-center gap-3 bg-white text-black hover:bg-gray-200 px-8 py-3 rounded-xl font-bold text-xs uppercase tracking-widest transition-all shadow-2xl shadow-white/5">
                        <i class="fas fa-download"></i> Descargar Credencial (PDF)
                    </button>
                </div>
            </div>

        </div>

    </div>

</div>

<style>
    @media print {
        /* Configuración de página */
        @page { size: portrait; margin: 0; }
        
        /* Ocultar todo por defecto */
        body * { visibility: hidden; }
        
        /* Mostrar solo el carnet y sus hijos */
        #agency-card, #agency-card * { visibility: visible; }
        
        #agency-card {
            position: fixed !important;
            left: 50% !important;
            top: 40% !important;
            transform: translateX(-50%) scale(1.2) !important;
            visibility: visible !important;
            display: flex !important;
            /* Forzar colores */
            background-color: #000 !important;
            background: linear-gradient(135deg, #1a1a1a 0%, #000000 100%) !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            border: none !important;
            box-shadow: none !important;
        }

        /* Asegurar visibilidad de elementos internos sobre fondo negro */
        #agency-card .text-white { color: white !important; }
        #agency-card .text-orange-500 { color: #ffaa00 !important; }
        #agency-card .bg-white { background-color: white !important; }
    }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Generar QR simplificado para mejorar lectura
        const qrData = "{{ $teamMember->cedula }}";
        new QRCode(document.getElementById("qrcode-container"), {
            text: qrData,
            width: 120, // Aumentado para mejor definición
            height: 120,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.M // Nivel medio es más fácil de leer que el alto (H)
        });

        // Lógica de Descarga PDF
        document.getElementById('download-btn').addEventListener('click', function() {
            const btn = this;
            const card = document.getElementById('agency-card');
            
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando...';

            html2canvas(card, {
                useCORS: true,
                scale: 5, // Aumentamos la escala para una nitidez extrema (Ultra High Definition)
                backgroundColor: null,
                logging: false,
                allowTaint: true,
                imageTimeout: 0
            }).then(canvas => {
                const imgData = canvas.toDataURL('image/png');
                const { jsPDF } = window.jspdf;
                
                // Crear PDF con tamaño del carnet aproximado (85.6mm x 53.98mm es el estándar, pero usamos escala visual)
                const pdf = new jsPDF({
                    orientation: 'portrait',
                    unit: 'mm',
                    format: [85, 135],
                    compress: true // Comprimir para PDF más eficiente sin perder calidad visual
                });

                const imgProps = pdf.getImageProperties(imgData);
                const pdfWidth = pdf.internal.pageSize.getWidth();
                const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;

                pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
                pdf.save('Carnet_{{ str_replace(' ', '_', $teamMember->name) }}.pdf');

                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-download"></i> Descargar Credencial (PDF)';
            }).catch(err => {
                console.error(err);
                alert('Error al generar el PDF');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-download"></i> Descargar Credencial (PDF)';
            });
        });
    });
</script>
@endsection
