<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-t">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Limbani') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <!-- Canvas de Fondo Constelación -->
        <canvas id="constellationCanvas" class="fixed top-0 left-0 w-full h-full -z-10 bg-[#0f1012]"></canvas>

        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative z-10">
            <div>
                <a href="/">
                     <h1 class="text-5xl font-black text-white uppercase tracking-tighter">Limbani<span class="text-orange-500">.</span></h1>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white/5 backdrop-blur-md border border-white/10 shadow-2xl overflow-hidden sm:rounded-3xl">
                {{ $slot }}
            </div>
        </div>

        <script>
            const canvas = document.getElementById('constellationCanvas');
            const ctx = canvas.getContext('2d');
            let width, height;
            let particles = [];

            // Configuración
            const particleCount = 60; // Cantidad de estrellas
            const connectionDistance = 150; // Distancia para conectar líneas
            const mouseDistance = 200; // Radio de interacción del mouse

            let mouse = { x: null, y: null };

            window.addEventListener('mousemove', (e) => {
                mouse.x = e.x;
                mouse.y = e.y;
            });

            window.addEventListener('resize', resize);

            function resize() {
                width = canvas.width = window.innerWidth;
                height = canvas.height = window.innerHeight;
            }

            class Particle {
                constructor() {
                    this.x = Math.random() * width;
                    this.y = Math.random() * height;
                    this.vx = (Math.random() - 0.5) * 0.5; // Velocidad X lenta
                    this.vy = (Math.random() - 0.5) * 0.5; // Velocidad Y lenta
                    this.size = Math.random() * 2 + 1;
                    this.opacity = Math.random() * 0.6 + 0.1; // Brillo aleatorio entre 0.1 y 0.7
                }

                update() {
                    this.x += this.vx;
                    this.y += this.vy;

                    // Rebotar en los bordes
                    if (this.x < 0 || this.x > width) this.vx *= -1;
                    if (this.y < 0 || this.y > height) this.vy *= -1;
                }

                draw() {
                    ctx.fillStyle = `rgba(255, 255, 255, ${this.opacity})`;
                    ctx.beginPath();
                    ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                    ctx.fill();
                }
            }

            function init() {
                resize();
                for (let i = 0; i < particleCount; i++) {
                    particles.push(new Particle());
                }
                animate();
            }

            function animate() {
                ctx.clearRect(0, 0, width, height);
                
                particles.forEach((p, index) => {
                    p.update();
                    p.draw();

                    // Conectar con el mouse
                    let dx = mouse.x - p.x;
                    let dy = mouse.y - p.y;
                    let distance = Math.sqrt(dx * dx + dy * dy);

                    if (distance < mouseDistance) {
                        ctx.strokeStyle = `rgba(249, 115, 22, ${1 - distance / mouseDistance})`; // Color Naranja Limbani
                        ctx.lineWidth = 1;
                        ctx.beginPath();
                        ctx.moveTo(p.x, p.y);
                        ctx.lineTo(mouse.x, mouse.y);
                        ctx.stroke();
                        
                        // Efecto magnético leve (opcional)
                        // p.x += dx * 0.01;
                        // p.y += dy * 0.01;
                    }

                    // Conectar con otras partículas cercanas
                    for (let j = index + 1; j < particles.length; j++) {
                        let p2 = particles[j];
                        let dx2 = p.x - p2.x;
                        let dy2 = p.y - p2.y;
                        let dist2 = Math.sqrt(dx2 * dx2 + dy2 * dy2);

                        if (dist2 < connectionDistance) {
                            ctx.strokeStyle = `rgba(255, 255, 255, ${0.1 * (1 - dist2 / connectionDistance)})`;
                            ctx.lineWidth = 0.5;
                            ctx.beginPath();
                            ctx.moveTo(p.x, p.y);
                            ctx.lineTo(p2.x, p2.y);
                            ctx.stroke();
                        }
                    }
                });

                requestAnimationFrame(animate);
            }

            init();
        </script>
    </body>
</html>