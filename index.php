<?php require_once 'helpers.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INJOE | Future Agency</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="src/style.css?v=<?php echo time(); ?>">
    
    <script>
        // Evita que el navegador restaure la posición del scroll al recargar
        if (history.scrollRestoration) {
            history.scrollRestoration = 'manual';
        }
        // Fuerza el scroll arriba antes de que la página se muestre
        window.onbeforeunload = function () {
            window.scrollTo(0, 0);
        }
    </script>
</head>
<body class="cyber-theme">

    <div class="fixed-background"></div>

    <header class="glass-header" id="main-header">
        <div class="container header-content">
            
            <a href="#" class="logo-area">
                <img src="/InjoeAgencia/public/logo-injoe.png" alt="INJOE" class="header-logo-img">
            </a>

            <nav class="desktop-nav">
                <a href="#agencia" class="nav-link">AGENCIA</a>
                <a href="#servicios" class="nav-link">SERVICIOS</a>
                <a href="#fundacion" class="nav-link">FUNDACIÓN</a>
                <a href="#contacto" class="nav-link">CONTACTO</a>
            </nav>

            <div class="header-actions">
                <a href="https://limbani.com" target="_blank" class="btn-limbani">
                    <i class="fas fa-lock"></i> ACCESO LIMBANI
                </a>
                <div class="menu-trigger" id="menuToggle">
                    <span class="menu-text">MENU</span>
                    <div class="hamburger"><span></span><span></span></div>
                </div>
            </div>
        </div>
    </header>

    <div class="cyber-overlay-menu" id="overlayMenu">
        <div class="container overlay-content">
            <div class="menu-header">
                <div class="close-btn" id="closeMenu">CERRAR <i class="fas fa-times"></i></div>
            </div>
            <div class="menu-grid">
                <div class="menu-links-col">
                    <nav class="overlay-nav">
                        <a href="#agencia" class="overlay-link">01. AGENCIA</a>
                        <a href="#servicios" class="overlay-link">02. SERVICIOS</a>
                        <a href="#fundacion" class="overlay-link">03. FUNDACIÓN</a>
                        <a href="#contacto" class="overlay-link">04. CONTACTO</a>
                    </nav>
                </div>
                <div class="menu-info-col">
                    <h3>PLATAFORMA</h3>
                    <p>Accede a tu panel de control Limbani.</p>
                    <a href="https://limbani.com" target="_blank" class="btn-limbani-large">
                        IR A LIMBANI <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <section class="hero-cyber">
        <div class="container hero-content-center">
            <p class="hero-cities">CALI | BOGOTÁ | MEDELLIN</p>
            <h1 class="hero-title-glow">SOLO LÁNZATE</h1>
            <div class="hero-elegant-text">
                <div class="cyber-line"></div> 
                <p>
                    En <span class="text-highlight">INJOE</span> damos vida a las marcas que quieren trascender.<br>
                    Conectamos ideas, personas y resultados reales.
                </p>
            </div>
        </div>
    </section>

    <div class="main-wrapper container" id="agencia">

        <div class="section-divider-center">
            <span class="neon-tag">AGENCIA</span>
            <h2 class="section-heading">NUESTRO UNIVERSO</h2>
            <p class="section-subtext">Aliados estratégicos y espacios de creación.</p>
        </div>

        <div class="tech-card full-width">
            <div class="card-internal-label">MARCAS QUE CONFÍAN EN EL FUTURO</div>
            <div class="logo-wall-inner">
                <?php
                $logopath = __DIR__ . '/public/logos/';
                if (is_dir($logopath)) {
                    $logos = glob($logopath . '*.{jpg,jpeg,png}', GLOB_BRACE);
                } else { $logos = []; }

                function renderLogoTrack($logosList, $reverse = false) {
                    $directionClass = $reverse ? 'reverse-track' : 'normal-track';
                    echo '<div class="ticker-row"><div class="ticker-track-img ' . $directionClass . '">';
                    if (!$logosList) { echo '<span style="padding:20px; color:#555;">(Sube logos a public/logos)</span>'; } 
                    else {
                        $fullList = array_merge($logosList, $logosList, $logosList); 
                        foreach ($fullList as $logoFile) {
                            $webPath = '/InjoeAgencia/public/logos/' . basename($logoFile);
                            echo '<img src="' . $webPath . '" alt="Logo" class="brand-logo">';
                        }
                    }
                    echo '</div></div>';
                }
                renderLogoTrack($logos, false);
                renderLogoTrack($logos, true);
                ?>
            </div>
        </div>

        <section class="tech-card hq-card">
            <div class="card-content-side">
                <div class="hq-status">
                    <span class="status-dot"></span> OPERATIVO
                </div>
                <h2 class="card-title">NUESTRO HQ</h2>
                <p class="card-desc-long">
                    Más que una oficina, este es nuestro <strong>laboratorio de ideas</strong>. 
                    Un espacio diseñado para romper barreras creativas, donde la tecnología 
                    y el diseño convergen para transformar conceptos abstractos en 
                    experiencias digitales tangibles.
                </p>
                <div class="location-badge">
                    <i class="fas fa-map-marker-alt"></i> CALI, COLOMBIA
                </div>
            </div>
            <div class="card-image-side">
                <img src="/InjoeAgencia/public/hero-main.jpeg" alt="Oficina Injoe" class="cover-img">
            </div>
        </section>

        <section class="services-section" id="servicios">
            <div class="section-divider-center">
                <span class="neon-tag">SOLUCIONES</span>
                <h2 class="section-heading">NUESTROS SERVICIOS</h2>
                <p class="section-subtext">Estrategias digitales diseñadas para el impacto.</p>
            </div>
            
            <div class="services-grid-4">
                <div class="tech-card service-card">
                    <div class="grid-bg" style="background-image: url('/InjoeAgencia/public/hero-brain.jpg');"></div>
                    <div class="service-content">
                        <div class="service-header">
                            <h3>SOCIAL MEDIA</h3>
                            <span class="service-sub">Estrategia, contenido y comunidad</span>
                        </div>
                        <div class="service-body">
                            <p>Gestionamos redes sociales con un enfoque estratégico y orientado a objetivos. Creamos contenido relevante que conecta con tu audiencia, fortalece tu marca y convierte seguidores en clientes.</p>
                        </div>
                    </div>
                </div>

                <div class="tech-card service-card">
                    <div class="grid-bg" style="background-image: url('/InjoeAgencia/public/service-branding.jpg');"></div>
                    <div class="service-content">
                        <div class="service-header">
                            <h3>BRANDING</h3>
                            <span class="service-sub">Construimos marcas con identidad</span>
                        </div>
                        <div class="service-body">
                            <p>Desarrollamos marcas sólidas, coherentes y memorables. Desde la conceptualización hasta la ejecución visual, alineamos tu identidad con tu propósito y mercado.</p>
                        </div>
                    </div>
                </div>

                <div class="tech-card service-card">
                    <div class="grid-bg" style="background-image: url('/InjoeAgencia/public/service-dev.jpg');"></div>
                    <div class="service-content">
                        <div class="service-header">
                            <h3>DESARROLLO WEB</h3>
                            <span class="service-sub">Sitios que comunican y venden</span>
                        </div>
                        <div class="service-body">
                            <p>Diseñamos y desarrollamos páginas web funcionales, estéticas y optimizadas para conversión. Cada sitio está pensado para reflejar la esencia de tu marca y guiar al usuario a la acción.</p>
                        </div>
                    </div>
                </div>

                <div class="tech-card service-card">
                    <div class="grid-bg" style="background-image: url('/InjoeAgencia/public/service-ads.png');"></div>
                    <div class="service-content">
                        <div class="service-header">
                            <h3>CAMPAÑAS ADS</h3>
                            <span class="service-sub">Publicidad digital de resultados</span>
                        </div>
                        <div class="service-body">
                            <p>Creamos y gestionamos campañas publicitarias en plataformas digitales para aumentar visibilidad, tráfico y conversiones. Estrategia, segmentación y análisis constante.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>

    <section class="foundation-section" id="fundacion">
        <div class="section-divider-center">
            <span class="neon-tag">
                <span class="status-dot-green-small"></span> PROPÓSITO SOCIAL
            </span>
            <h2 class="section-heading">FUNDACIÓN INJOE</h2>
            <p class="section-subtext">Comunicación para transformar realidades.</p>
        </div>

        <div class="tech-card foundation-card">
            <div class="foundation-content">
                <p class="foundation-text">
                    Desde <span class="text-highlight">INJOE Fundación</span> creemos en el poder de la comunicación para transformar realidades. Desarrollamos y apoyamos iniciativas sociales enfocadas en impacto comunitario, educación y bienestar.
                </p>
                
                <div class="social-wall-preview">
                    <a href="https://www.instagram.com/injoefundacion/" target="_blank" class="social-item">
                        <img src="/InjoeAgencia/public/fund1.png" alt="Jornada Social">
                        <div class="social-overlay"><i class="fab fa-instagram"></i></div>
                    </a>
                    <a href="https://www.instagram.com/injoefundacion/" target="_blank" class="social-item">
                        <img src="/InjoeAgencia/public/fund2.png" alt="Actividad Fundación">
                        <div class="social-overlay"><i class="fab fa-instagram"></i></div>
                    </a>
                    <a href="https://www.instagram.com/injoefundacion/" target="_blank" class="social-item">
                        <img src="/InjoeAgencia/public/fund3.png" alt="Evento">
                        <div class="social-overlay"><i class="fab fa-instagram"></i></div>
                    </a>
                    <a href="https://www.instagram.com/injoefundacion/" target="_blank" class="social-item">
                        <img src="/InjoeAgencia/public/fund4.png" alt="Comunidad">
                        <div class="social-overlay"><i class="fab fa-instagram"></i></div>
                    </a>
                </div>

                <p class="foundation-text-highlight">
                    Cada proyecto es una oportunidad para servir, inspirar y generar cambio real.
                </p>
                
                <div class="button-wrapper">
                    <a href="https://injoefundacion.com" target="_blank" class="cyber-button">
                        CONOCE NUESTRAS JORNADAS <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="contact-section" id="contacto">
        <div class="section-divider-center">
            <span class="neon-tag">
                <span class="status-dot-green-small"></span> CONTACTO DIRECTO
            </span>
            <h2 class="section-heading">HABLEMOS AHORA</h2>
        </div>

        <div class="tech-card contact-card-simple">
            <div class="contact-direct-content">
                <div class="whatsapp-hero-icon">
                    <i class="fab fa-whatsapp"></i>
                    <div class="hero-pulse"></div>
                </div>
                <h3 class="contact-title-direct">SIN FORMULARIOS. SIN ESPERAS.</h3>
                <p class="contact-intro-simple">
                    En <span class="text-highlight">INJOE</span> valoramos tu tiempo. 
                    Conecta directamente con nuestro centro de administración e innovación.
                    Estamos listos para escucharte.
                </p>
                <a href="https://wa.me/573234004026?text=Hola%20INJOE%2C%20quiero%20iniciar%20un%20proyecto%20con%20ustedes." target="_blank" class="cyber-button-large">
                    INICIAR CHAT DE NEGOCIOS <i class="fas fa-arrow-right"></i>
                </a>
                <p class="direct-number">+57 323 400 4026</p>
            </div>
        </div>
    </section>

    <footer class="cyber-footer-minimal">
        <div class="cyber-line-top"></div>
        <div class="container">
            <div class="footer-grid-minimal">
                <div class="footer-brand">
                    <div class="footer-logo">INJOE<span class="highlight">©2026</span></div>
                    <p class="footer-slogan">Creamos el futuro que otros solo imaginan.</p>
                </div>
                <div class="footer-data">
                    <h4 class="footer-label">DATA CENTER</h4>
                    <ul class="data-list">
                        <li><i class="fas fa-envelope"></i> <a href="mailto:info@injoe.com">INFO@INJOE.COM</a></li>
                        <li><i class="fas fa-map-marker-alt"></i> <span>CALI, COLOMBIA</span></li>
                        <li><i class="fab fa-whatsapp"></i> <a href="https://wa.me/573234004026" target="_blank">+57 323 400 4026</a></li>
                    </ul>
                </div>
                <div class="footer-social-minimal">
                    <h4 class="footer-label">REDES</h4>
                    <div class="social-icons-row">
                        <a href="#" class="minimal-icon"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="minimal-icon"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="minimal-icon"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom-minimal">
                <p>© 2026 INJOE AGENCIA. TODOS LOS DERECHOS RESERVADOS.</p>
                <p class="made-by">HECHO EN EL FUTURO <i class="fas fa-bolt"></i></p>
            </div>
        </div>
    </footer>

    <a href="https://wa.me/573234004026" target="_blank" class="whatsapp-btn-float">
        <i class="fab fa-whatsapp"></i>
        <div class="pulse-ring"></div>
    </a>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // FORZAR SCROLL AL INICIO AL CARGAR (IMPORTANTE)
            if (window.location.hash) {
                // Si hay un hash (#fundacion), limpiarlo de la URL sin recargar
                history.replaceState(null, null, ' ');
            }
            window.scrollTo(0, 0);

            // Variables del menú
            const header = document.getElementById('main-header');
            const menuBtn = document.getElementById('menuToggle');
            const closeBtn = document.getElementById('closeMenu');
            const overlay = document.getElementById('overlayMenu');
            const links = document.querySelectorAll('.overlay-link, .nav-link');

            // Abrir menú
            menuBtn.addEventListener('click', () => {
                overlay.classList.add('active');
            });

            // Cerrar menú
            closeBtn.addEventListener('click', () => {
                overlay.classList.remove('active');
            });

            // Cerrar al dar clic en link
            links.forEach(link => {
                link.addEventListener('click', () => {
                    overlay.classList.remove('active');
                });
            });

            // Efecto Header Scroll
            window.addEventListener('scroll', () => {
                if (window.scrollY > 50) {
                    header.classList.add('scrolled');
                } else {
                    header.classList.remove('scrolled');
                }
            });

            // Scroll Suave (Smooth Scroll)
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href');
                    const targetSection = document.querySelector(targetId);
                    if (targetSection) {
                        targetSection.scrollIntoView({ behavior: 'smooth' });
                    }
                });
            });
        });
    </script>

</body>
</html>