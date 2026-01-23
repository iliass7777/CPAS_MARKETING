// Dark mode toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    const themeToggle = document.getElementById('theme-toggle');
    const html = document.documentElement;

    // Check for saved theme preference or default to light mode
    const currentTheme = localStorage.getItem('theme') || 'light';
    if (currentTheme === 'dark') {
        html.classList.remove('light');
        html.classList.add('dark');
    }
    updateToggleIcon(currentTheme);

    function updateToggleIcon(theme) {
        const icon = themeToggle.querySelector('.material-symbols-outlined');
        icon.textContent = theme === 'dark' ? 'light_mode' : 'dark_mode';
    }

    themeToggle.addEventListener('click', () => {
        const isDark = html.classList.contains('dark');
        if (isDark) {
            html.classList.remove('dark');
            html.classList.add('light');
            localStorage.setItem('theme', 'light');
            updateToggleIcon('light');
        } else {
            html.classList.remove('light');
            html.classList.add('dark');
            localStorage.setItem('theme', 'dark');
            updateToggleIcon('dark');
        }
    });
});
