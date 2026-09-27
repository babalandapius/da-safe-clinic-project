
document.addEventListener('DOMContentLoaded', function() {
    const themeToggleBtn = document.getElementById('theme-toggle');
    const body = document.body;

    console.log('Theme toggle script loaded');
    console.log('Theme toggle button found:', themeToggleBtn);

    if (!themeToggleBtn) {
        console.error('Theme toggle button not found!');
        return;
    }

    const themeIcon = themeToggleBtn.querySelector('i');
    console.log('Theme icon found:', themeIcon);

    // Check for saved theme preference or default to light mode
    const currentTheme = localStorage.getItem('theme') || 'light';
    console.log('Current theme from localStorage:', currentTheme);
    setTheme(currentTheme);

    // Theme toggle button click handler
    themeToggleBtn.addEventListener('click', function() {
        console.log('Theme toggle button clicked');
        const newTheme = body.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
        console.log('Switching to theme:', newTheme);
        setTheme(newTheme);
    });

    function setTheme(theme) {
        console.log('Setting theme to:', theme);
        body.setAttribute('data-theme', theme);
        localStorage.setItem('theme', theme);

        // Update button appearance and icon
        if (theme === 'dark') {
            themeToggleBtn.classList.add('dark-mode');
            if (themeIcon) themeIcon.className = 'fas fa-sun';
            themeToggleBtn.title = 'Switch to Light Mode';
        } else {
            themeToggleBtn.classList.remove('dark-mode');
            if (themeIcon) themeIcon.className = 'fas fa-moon';
            themeToggleBtn.title = 'Switch to Dark Mode';
        }
        console.log('Theme set successfully');
    }
});