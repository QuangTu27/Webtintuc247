document.addEventListener('DOMContentLoaded', () => {
    
    const userInfoToggle = document.getElementById('userInfoToggle');
    if (userInfoToggle) {
        userInfoToggle.addEventListener('click', toggleUserMenu);
    }
    
    document.addEventListener('click', function(e) {
        const dropdown = document.querySelector('.user-dropdown');
        if (dropdown && !dropdown.contains(e.target)) {
            const menu = document.getElementById('userMenu');
            if (menu) menu.style.display = 'none';
        }
    });

    const logoutLink = document.getElementById('logoutLink');
    if (logoutLink) {
        logoutLink.addEventListener('click', function(e) {
            if (!confirm('Bạn có chắc muốn đăng xuất?')) {
                e.preventDefault();
            }
        });
    }

});

function toggleUserMenu() {
    const menu = document.getElementById('userMenu');
    if (menu) {
        menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
    }
}
