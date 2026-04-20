document.addEventListener('DOMContentLoaded', function() {
    const registerForm = document.getElementById('register-form');
    if (registerForm) {
        registerForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const hoten = document.getElementById('reg-hoten').value;
            const username = document.getElementById('reg-username').value;
            const email = document.getElementById('reg-email').value;
            const password = document.getElementById('reg-password').value;
            const confirmPassword = document.getElementById('reg-confirm-password').value;

            if (password !== confirmPassword) {
                alert('Mật khẩu nhập lại không khớp!');
                return;
            }

            try {
                const res = await fetch(AUTH_BASE_URL + 'api/site/auth/register', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ hoten, username, email, password })
                });
                const data = await res.json();
                if (data.status === 'success') {
                    alert(data.message);
                    switchTab('login');
                } else {
                    alert('Lỗi: ' + data.message);
                }
            } catch(err) {
                alert('Lỗi kết nối máy chủ!');
            }
        });
    }

    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const username = document.getElementById('login-username').value;
            const password = document.getElementById('login-password').value;

            try {
                const res = await fetch(AUTH_BASE_URL + 'api/site/auth/login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ username, password })
                });
                const data = await res.json();
                if (data.status === 'success') {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        window.location.reload();
                    }
                } else {
                    alert('Lỗi: ' + data.message);
                }
            } catch(err) {
                alert('Lỗi kết nối máy chủ!');
            }
        });
    }
});

function openAuthModal(mode = 'login') {
    document.getElementById('auth-modal').style.display = 'flex';
    switchTab(mode);
}

function closeAuthModal() {
    document.getElementById('auth-modal').style.display = 'none';
}

function switchTab(tabName) {
    const tabs = document.querySelectorAll('.tab-item');
    tabs.forEach(t => t.classList.remove('active'));
    document.getElementById('login-form').classList.remove('active');
    document.getElementById('register-form').classList.remove('active');

    if (tabName === 'login') {
        tabs[0].classList.add('active');
        document.getElementById('login-form').classList.add('active');
    } else {
        tabs[1].classList.add('active');
        document.getElementById('register-form').classList.add('active');
    }
}

window.onclick = function(event) {
    let modal = document.getElementById('auth-modal');
    if (event.target == modal) { closeAuthModal(); }
}

document.querySelectorAll('.toggle-password').forEach(item => {
    item.addEventListener('click', function() {
        let input = this.previousElementSibling;
        if (input.type === "password") {
            input.type = "text";
            this.classList.remove('fa-eye');
            this.classList.add('fa-eye-slash');
        } else {
            input.type = "password";
            this.classList.remove('fa-eye-slash');
            this.classList.add('fa-eye');
        }
    });
});
