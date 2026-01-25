document.addEventListener('DOMContentLoaded', () => {
    
    // 1. EFECTO SCROLL HEADER
    const header = document.getElementById('main-header');
    
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });

    // 2. MENÚ OVERLAY (ABRIR / CERRAR)
    const menuTrigger = document.getElementById('menuToggle');
    const closeBtn = document.getElementById('closeMenu');
    const overlayMenu = document.getElementById('overlayMenu');
    const overlayLinks = document.querySelectorAll('.overlay-link');

    // Abrir
    menuTrigger.addEventListener('click', () => {
        overlayMenu.classList.add('active');
    });

    // Cerrar con botón X
    closeBtn.addEventListener('click', () => {
        overlayMenu.classList.remove('active');
    });

    // Cerrar al dar clic en un enlace (Navegación suave)
    overlayLinks.forEach(link => {
        link.addEventListener('click', () => {
            overlayMenu.classList.remove('active');
        });
    });

});