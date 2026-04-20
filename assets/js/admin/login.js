document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', handleLogin);
    }
});

async function handleLogin(e) {
    if (e) e.preventDefault();
    
    const u = document.getElementById('username').value;
    const p = document.getElementById('password').value;
    const err = document.getElementById('error-msg');
    const btn = document.getElementById('btnLogin');
    
    err.style.display = 'none';
    btn.innerHTML = 'ĐANG XỬ LÝ...';
    btn.disabled = true;

    try {
        const response = await fetch(BASE_URL + 'api/admin/auth/login', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({username: u, password: p})
        });
        
        const result = await response.json();
        
        if (result.status === 'success') {
            window.location.href = BASE_URL + 'admin/dashboard';
        } else {
            err.innerHTML = result.message;
            err.style.display = 'block';
            btn.innerHTML = 'ĐĂNG NHẬP';
            btn.disabled = false;
        }
    } catch(errObj) {
        err.innerHTML = "Lỗi kết nối máy chủ!";
        err.style.display = 'block';
        btn.innerHTML = 'ĐĂNG NHẬP';
        btn.disabled = false;
    }
}
