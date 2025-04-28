import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    const html = document.documentElement;
    const toggleButtons = document.querySelectorAll('.toggle-mode');

    const savedMode = localStorage.getItem('mode')
    const darkSystemPreference = window.matchMedia('(prefers-color-scheme: dark)').matches;

    if (savedMode === 'dark' || (!savedMode && darkSystemPreference)) html.classList.add('dark');
    else html.classList.remove('dark');

    toggleButtons.forEach(button => {
        if (button) {
            button.addEventListener('click', () => {
                const isDark = html.classList.toggle('dark'); // Adds/removes 'dark' from the html element and returns true/false on click
                localStorage.setItem('mode', isDark ? 'dark' : 'light');
            });
        }
    });
});