document.addEventListener('DOMContentLoaded', () => {
    loadProfileData();

    const avatarInput = document.getElementById('avatar');
    if (avatarInput) {
        avatarInput.addEventListener('change', function() {
            previewImage(this);
        });
    }

    const profileForm = document.getElementById('profileForm');
    if (profileForm) {
        profileForm.addEventListener('submit', (e) => {
            e.preventDefault();
            updateProfile();
        });
    }
});

async function loadProfileData() {
    try {
        const response = await fetch(BASE_URL + 'admin/profile/data');
        const result = await response.json();

        if (result.status === 'success') {
            const user = result.data;
            document.getElementById('username').value = user.username;
            document.getElementById('hoten').value = user.hoten;
            document.getElementById('email').value = user.email || '';
            document.getElementById('role').value = user.role;
            document.getElementById('created_at').value = user.created_at || 'Không xác định';

            if (user.avatar) {
                document.getElementById('avatarPreview').src = BASE_URL + 'assets/images/avatars/' + user.avatar;
            }

            document.getElementById('loading').style.display = 'none';
            document.getElementById('profileForm').style.display = 'block';
        } else {
            alert(result.message);
        }
    } catch(e) { 
        alert('Lỗi lấy dữ liệu API'); 
    }
}

function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) { 
            document.getElementById('avatarPreview').src = e.target.result; 
        }
        reader.readAsDataURL(input.files[0]);
    }
}

async function updateProfile() {
    const formData = new FormData();
    formData.append('hoten', document.getElementById('hoten').value);
    formData.append('email', document.getElementById('email').value);
    formData.append('password', document.getElementById('password').value);

    const fileInput = document.getElementById('avatar');
    if (fileInput.files && fileInput.files.length > 0) {
        formData.append('avatar', fileInput.files[0]);
    }

    try {
        const response = await fetch(BASE_URL + 'admin/profile/update', { 
            method: 'POST', 
            body: formData 
        });
        const result = await response.json();

        const msgDiv = document.getElementById('status-msg');
        msgDiv.style.display = 'block';
        if (result.status === 'success') {
            msgDiv.className = 'alert alert-success';
            msgDiv.innerHTML = '✅ Cập nhật thông tin cá nhân thành công!';
            document.getElementById('password').value = '';
            
            // Cập nhật ngay avatar ở Header nếu có thay đổi
            if (result.avatar) {
                const headerAvatar = document.getElementById('headerAvatar');
                if (headerAvatar) {
                    headerAvatar.src = BASE_URL + 'assets/images/avatars/' + result.avatar;
                }
            }
            // Cập nhật tên ở Header
            const headerNameSidebar = document.getElementById('headerNameSidebar');
            if (headerNameSidebar) headerNameSidebar.innerText = document.getElementById('hoten').value;
            const headerNameTopbar = document.getElementById('headerNameTopbar');
            if (headerNameTopbar) headerNameTopbar.innerHTML = `Xin chào, <b>${document.getElementById('hoten').value}</b>`;
            
        } else {
            msgDiv.className = 'alert alert-warning';
            msgDiv.innerHTML = '❌ Lỗi: ' + result.message;
        }
        setTimeout(() => msgDiv.style.display = 'none', 3000);
    } catch(e) { 
        alert('Lỗi gọi Server API'); 
    }
}
