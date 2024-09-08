import './bootstrap';

// Enable CSP and remove inline script for logout menu item
document.addEventListener('DOMContentLoaded', function () {
    const logoutLink = document.getElementById('logout-link');
    if (!logoutLink) {
        return;
    }
    logoutLink.addEventListener('click', function (event) {
        event.preventDefault();
        document.getElementById('logout-form').submit();
    });
});
