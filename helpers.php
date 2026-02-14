<?php
function vite($script = 'main.js') {
    // Detectar si estamos en local o en producción
    $isLocal = in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1']);

    // Entorno Local (Desarrollo)
    if ($isLocal) {
        $handle = @fsockopen('localhost', 5173);
        if ($handle) {
            fclose($handle);
            return '<script type="module" src="http://localhost:5173/' . $script . '"></script>';
        }
    }

    // Entorno Producción (Hostinger)
    $manifestPath = __DIR__ . '/dist/manifest.json'; // Ajuste de ruta común en Hostinger
    if (!file_exists($manifestPath)) {
        $manifestPath = __DIR__ . '/dist/.vite/manifest.json';
    }
    if (file_exists($manifestPath)) {
        $manifest = json_decode(file_get_contents($manifestPath), true);
        if (isset($manifest[$script])) {
            $file = $manifest[$script]['file'];
            $cssFiles = $manifest[$script]['css'] ?? [];
            $html = '<script type="module" src="./dist/' . $file . '"></script>';
            foreach ($cssFiles as $css) {
                $html .= '<link rel="stylesheet" href="./dist/' . $css . '">';
            }
            return $html;
        }
    }
}
?>