<?php
// helpers/lang.php

// 1. Iniciar sesión si no está iniciada (por seguridad)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Detectar si el usuario quiere cambiar el idioma (por URL)
if (isset($_GET['lang'])) {
    $lang = $_GET['lang'];
    // Solo permitimos 'es' o 'en' para evitar hackeos
    if (in_array($lang, ['es', 'en'])) {
        $_SESSION['lang'] = $lang;
    }
}

// 3. Definir el idioma actual (Prioridad: Sesión > Defecto 'es')
$current_lang = $_SESSION['lang'] ?? 'es';

// 4. Cargar el diccionario correspondiente
// Asumimos que helpers está al mismo nivel que config
$lang_file = __DIR__ . "/../config/lang/{$current_lang}.php";

if (file_exists($lang_file)) {
    $texts = include $lang_file;
} else {
    $texts = []; // Array vacío si falla algo
}

// 5. Función global para usar en las Vistas
// Ejemplo de uso: <?= lang('titulo_inicio') 
function lang($key) {
    global $texts;
    return $texts[$key] ?? $key; // Si no encuentra la traducción, devuelve la clave
}
?>