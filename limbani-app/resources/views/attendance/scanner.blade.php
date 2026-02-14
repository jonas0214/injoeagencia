@extends('layouts.asana')

@section('content')
<style>
    body {
        background: #0f1012 !important;
        min-height: 100vh;
        color: white;
        overflow-x: hidden;
    }
    .scanner-container {
        background: rgba(255, 255, 255, 0.03);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 2.5rem;
    }
    #reader {
        width: 100%;
        border-radius: 1.5rem;
        overflow: hidden;
        border: none !important;
    }
    #reader video {
        border-radius: 1.5rem;
        object-fit: cover;
    }
    .status-badge {
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .scan-glow {
        animation: scan-pulse 2s infinite;
    }
    @keyframes scan-pulse {
        0% { box-shadow: 0 0 0 0 rgba(249, 115, 22, 0.4); }
        70% { box-shadow: 0 0 0 20px rgba(249, 115, 22, 0); }
        100% { box-shadow: 0 0 0 0 rgba(249, 115, 22, 0); }
    }
</style>

<div class="py-6 md:py-12 px-4 md:px-8 max-w-[800px] mx-auto text-center">
    
    <div class="mb-8 md:mb-12">
        <span class="text-orange-500 text-[10px] md:text-xs font-black uppercase tracking-[0.4em] mb-2 md:mb-4 block">Intelligence Division</span>
        <h1 class="text-2xl md:text-4xl font-black text-white uppercase italic tracking-tighter">Control de Asistencia<span class="text-orange-500 not-italic">.</span></h1>
        <p class="text-gray-500 mt-2 md:mt-4 text-xs md:text-sm tracking-widest font-medium">Escanea el código QR de tu credencial</p>
    </div>

    <div class="scanner-container p-4 md:p-8 shadow-2xl relative">
        <!-- Decoración -->
        <div class="absolute -top-10 -right-10 w-32 h-32 bg-orange-500/10 rounded-full blur-[60px]"></div>
        <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-orange-500/5 rounded-full blur-[60px]"></div>

        <div class="relative z-10">
            <!-- Visor del Scanner -->
            <div class="mb-8 border-2 border-dashed border-white/10 p-2 rounded-[2rem]">
                <div id="reader" class="scan-glow"></div>
            </div>

            <!-- Feedback de Estado -->
            <div id="status-card" class="hidden p-4 md:p-6 rounded-2xl border transition-all duration-500">
                <div class="flex flex-col sm:flex-row items-center sm:items-start gap-4 md:gap-6 text-center sm:text-left">
                    <div id="member-photo" class="w-16 h-16 md:w-20 md:h-20 rounded-2xl bg-white/5 border border-white/10 overflow-hidden flex-shrink-0">
                        <!-- Imagen vía JS -->
                    </div>
                    <div>
                        <h3 id="member-name" class="text-lg md:text-xl font-bold text-white mb-1">Cargando...</h3>
                        <p id="scan-message" class="text-xs md:text-sm font-medium uppercase tracking-widest"></p>
                        <p id="scan-time" class="text-[10px] md:text-xs text-gray-500 mt-2 font-mono"></p>
                    </div>
                </div>
            </div>

            <!-- Mensaje de Espera -->
            <div id="idle-message" class="py-8">
                <div class="flex flex-col items-center gap-4">
                    <div class="w-12 h-12 rounded-full border-2 border-t-orange-500 border-white/5 animate-spin"></div>
                    <p class="text-xs font-bold text-gray-600 uppercase tracking-[0.3em]">Esperando escaneo...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Acceso rápido -->
    <div class="mt-12 flex justify-center gap-6">
        <a href="{{ route('attendance.index') }}" class="text-[10px] font-bold text-gray-500 uppercase tracking-widest hover:text-orange-500 transition-colors">
            <i class="fas fa-list-ul mr-2"></i> Ver Registro de Hoy
        </a>
    </div>

</div>

<!-- Audio para feedback -->
<audio id="audio-success" src="https://assets.mixkit.co/active_storage/sfx/2568/2568-preview.mp3"></audio>

<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    const html5QrCode = new Html5Qrcode("reader");
    const statusCard = document.getElementById('status-card');
    const idleMessage = document.getElementById('idle-message');
    const audioSuccess = document.getElementById('audio-success');

    let isScanning = true;

    function onScanSuccess(decodedText, decodedResult) {
        if (!isScanning) return;
        
        isScanning = false;
        console.log(`Scan Result: ${decodedText}`);

        // Ahora el QR solo contiene la cédula, lo enviamos directamente
        const cedula = decodedText.trim();

        if (cedula && cedula.length >= 5) {
            registerAttendance(cedula);
        } else {
            showError("Formato de QR no válido");
            setTimeout(() => { isScanning = true; }, 2000);
        }
    }

    function registerAttendance(cedula) {
        fetch("{{ route('attendance.scan') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ cedula: cedula })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                audioSuccess.play();
                showStatus(data);
            } else {
                showError(data.message || "Error al registrar");
            }
        })
        .catch(error => {
            showError("Error de conexión");
        })
        .finally(() => {
            setTimeout(() => {
                resetUI();
                isScanning = true;
            }, 5000); // 5 segundos de feedback antes de volver a escanear
        });
    }

    function showStatus(data) {
        idleMessage.classList.add('hidden');
        statusCard.classList.remove('hidden');
        
        const isEntry = data.type === 'in';
        statusCard.className = `p-6 rounded-3xl border transition-all duration-500 ${isEntry ? 'bg-green-500/10 border-green-500/30' : 'bg-orange-500/10 border-orange-500/30'}`;
        
        document.getElementById('member-name').innerText = data.member;
        document.getElementById('scan-message').innerText = data.message;
        document.getElementById('scan-message').className = `text-sm font-black uppercase tracking-widest ${isEntry ? 'text-green-500' : 'text-orange-500'}`;
        document.getElementById('scan-time').innerText = `HORA REGISTRADA: ${data.time}`;
    }

    function showError(msg) {
        alert(msg);
    }

    function resetUI() {
        statusCard.classList.add('hidden');
        idleMessage.classList.remove('hidden');
    }

    const config = { fps: 10, qrbox: { width: 250, height: 250 } };
    html5QrCode.start({ facingMode: "user" }, config, onScanSuccess);
</script>
@endsection
