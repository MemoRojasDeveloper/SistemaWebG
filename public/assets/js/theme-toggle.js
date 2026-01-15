// public/assets/js/theme-toggle.js

document.addEventListener('DOMContentLoaded', () => {
    const toggleBtn = document.getElementById('darkModeBtn');
    const icon = toggleBtn.querySelector('i');
    const htmlElement = document.documentElement;

    // 1. Revisar si hay una preferencia guardada en localStorage
    const savedTheme = localStorage.getItem('theme');
    
    if (savedTheme) {
        htmlElement.setAttribute('data-bs-theme', savedTheme);
        updateIcon(savedTheme);
    } else {
        // Opcional: Detectar preferencia del sistema operativo
        // htmlElement.setAttribute('data-bs-theme', 'light'); 
    }

    // 2. Función para cambiar el icono
    function updateIcon(theme) {
        if (theme === 'dark') {
            icon.classList.remove('bi-moon-stars-fill');
            icon.classList.add('bi-sun-fill');
            icon.classList.add('text-warning'); // Sol amarillo
            toggleBtn.setAttribute('title', 'Cambiar a modo claro');
        } else {
            icon.classList.remove('bi-sun-fill');
            icon.classList.remove('text-warning');
            icon.classList.add('bi-moon-stars-fill');
            toggleBtn.setAttribute('title', 'Cambiar a modo oscuro');
        }
    }

    // 3. Evento Click
    toggleBtn.addEventListener('click', () => {
        const currentTheme = htmlElement.getAttribute('data-bs-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

        htmlElement.setAttribute('data-bs-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        updateIcon(newTheme);
    });
});