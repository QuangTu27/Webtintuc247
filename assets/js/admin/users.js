// HÀM TOÀN CỤC THÔNG BÁO
function showNotification(text, type = 'success') {
    let msgDiv = document.getElementById('status-msg');
    
    if (!msgDiv) {
        msgDiv = document.createElement('div');
        msgDiv.id = 'status-msg';
        msgDiv.style.cssText = 'padding: 15px; margin-bottom: 20px; border-radius: 4px; color: white; font-weight: bold; transition: opacity 0.5s; z-index: 9999;';
        
        const title = document.querySelector('.admin-title');
        if (title) {
            title.after(msgDiv);
        } else {
            document.body.prepend(msgDiv);
        }
    }

    msgDiv.style.backgroundColor = type === 'success' ? '#00b686' : '#f44336';
    msgDiv.innerHTML = type === 'success' ? `✅ ${text}` : `❌ ${text}`;
    msgDiv.style.display = 'block';
    msgDiv.style.opacity = '1';

    setTimeout(() => {
        msgDiv.style.opacity = '0';
        setTimeout(() => { msgDiv.style.display = 'none'; }, 500);
    }, 3000);
}

// KHỞI TẠO CÁC SỰ KIỆN KHI DOM SẴN SÀNG
document.addEventListener('DOMContentLoaded', () => {
    
    // Đọc thống báo từ tham số trên URL
    const urlParams = new URLSearchParams(window.location.search);
    const msg = urlParams.get('msg'); 

    if (msg) {
        if (msg === 'added') showNotification('Thêm người dùng thành công!', 'success');
        else if (msg === 'updated') showNotification('Cập nhật thông tin thành công!', 'success');
        else if (msg === 'error') showNotification('Có lỗi xảy ra, vui lòng thử lại!', 'error');

        window.history.replaceState({}, document.title, window.location.pathname);
    }

    // ---------- TRANG DANH SÁCH ----------
    const tbody = document.getElementById('apiTableBody');
    if (tbody) {
        loadUsers();

        const searchForm = document.getElementById('searchForm');
        if (searchForm) {
            searchForm.addEventListener('submit', (e) => {
                e.preventDefault();
                loadUsers();
            });
        }

        const btnResetSearch = document.getElementById('btnResetSearch');
        if (btnResetSearch) {
            btnResetSearch.addEventListener('click', () => {
                const searchInput = document.getElementById('searchInput');
                if (searchInput) searchInput.value = '';
                loadUsers();
            });
        }

        const checkAll = document.getElementById('checkAll');
        if (checkAll) {
            checkAll.addEventListener('change', function() {
                document.querySelectorAll('.user-check:not([disabled])').forEach(cb => cb.checked = this.checked);
                updateDeleteBtn();
            });
        }

        const btnDeleteSelected = document.getElementById('btnDeleteSelected');
        if (btnDeleteSelected) btnDeleteSelected.addEventListener('click', deleteMultiple);

        tbody.addEventListener('change', (e) => {
            if (e.target.classList.contains('user-check')) updateDeleteBtn();
        });

        tbody.addEventListener('click', (e) => {
            if (e.target.classList.contains('btn-delete-item')) {
                const id = e.target.getAttribute('data-id');
                if (id) deleteUser(id);
            }
        });
    }

    // ---------- TRANG THÊM/SỬA ----------
    const addForm = document.getElementById('addForm');
    if (addForm) {
        addForm.addEventListener('submit', (e) => {
            e.preventDefault();
            submitUserForm();
        });
    }

    const editForm = document.getElementById('editForm');
    if (editForm) {
        if (typeof USER_ID !== 'undefined' && USER_ID > 0) {
            initUserForm();
        }
        editForm.addEventListener('submit', (e) => {
            e.preventDefault();
            submitUserForm();
        });
    }
});

let currentAdminId = 0;

// ---------- API CỦA TRANG DANH SÁCH ----------
async function loadUsers() {
    const keywordInput = document.getElementById('searchInput');
    const keyword = keywordInput ? encodeURIComponent(keywordInput.value.trim()) : '';

    const tbody = document.getElementById('apiTableBody');
    if (!tbody) return;
    
    tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 40px;">Đang tải dữ liệu...</td></tr>';

    try {
        const response = await fetch(BASE_URL + `admin/users/data?keyword=${keyword}`);
        const result = await response.json();

        if (result.status === 'success') {
            currentAdminId = result.auth.userId;
            renderTable(result.data, currentAdminId);
        } else {
            tbody.innerHTML = `<tr><td colspan="6" style="color:red; text-align:center;">${result.message}</td></tr>`;
        }
    } catch(e) {
        tbody.innerHTML = '<tr><td colspan="6" style="color:red; text-align:center;">Lỗi kết nối máy chủ!</td></tr>';
    }
}

function renderTable(data, currentAdminId) {
    const tbody = document.getElementById('apiTableBody');
    if (!tbody) return;
    
    tbody.innerHTML = '';
    const checkAll = document.getElementById('checkAll');
    if (checkAll) checkAll.checked = false;
    updateDeleteBtn();

    if (data.length === 0) {
        tbody.innerHTML = `<tr><td colspan="6" style="text-align:center; padding:30px; color:#999;">Không có người dùng nào.</td></tr>`;
        return;
    }

    let html = '';
    data.forEach(row => {
        let roleHtml = '';
        if (row.role === 'admin') roleHtml = '<span class="status-badge" style="background:#fce4ec; color:#c2185b;">Admin</span>';
        else if (row.role === 'phongvien') roleHtml = '<span class="status-badge" style="background:#fff8e1; color:#f57f17;">Phóng Viên</span>';
        else if (row.role === 'editor') roleHtml = '<span class="status-badge" style="background:#e8f5e9; color:#2e7d32;">Biên Tập Viên</span>';
        else if (row.role === 'nhabao') roleHtml = '<span class="status-badge" style="background:#f3e5f5; color:#7b1fa2;">Nhà Báo</span>';
        else if (row.role === 'ctv') roleHtml = '<span class="status-badge" style="background:#e0f2f1; color:#00796b;">Cộng Tác Viên</span>';
        else roleHtml = '<span class="status-badge" style="background:#e3f2fd; color:#1565c0;">Người dùng</span>';
        let isSelf = (row.id == currentAdminId);
        
        let actions = `<a class="btn btn-edit" href="${BASE_URL}admin/users/edit/${row.id}">✏️ Sửa</a>`;
        
        // Không gửi phép xóa chính mình (nút sẽ mờ)
        if (isSelf) {
            actions += `<button type="button" class="btn btn-delete btn-disabled" disabled style="opacity:0.5" title="Bạn không thể xóa chính mình">🗑️ Xoá</button>`;
        } else {
            actions += `<button type="button" class="btn btn-delete btn-delete-item" data-id="${row.id}">🗑️ Xoá</button>`;
        }

        let avatar = row.avatar ? `<img src="${BASE_URL}assets/images/avatars/${row.avatar}" style="width:30px; height:30px; border-radius:50%; vertical-align:middle; margin-right:10px; object-fit:cover;">` : `<div style="display:inline-block; width:30px; height:30px; border-radius:50%; background:#ccc; vertical-align:middle; margin-right:10px;"></div>`;

        html += `
            <tr>
                <td><input type="checkbox" name="ids[]" class="user-check" value="${row.id}" ${isSelf ? 'disabled title="Bạn không thể xóa chính mình"':''}></td>
                <td>${row.id}</td>
                <td><strong style="color:#d32f2f;">${escapeHtml(row.username)}</strong> ${isSelf ? '<span style="font-size:11px; background:#e0e0e0; padding:2px 5px; border-radius:10px;">Bạn</span>' : ''}</td>
                <td style="font-weight: 500;">${avatar} ${escapeHtml(row.hoten)}</td>
                <td>${roleHtml} <br><div style="font-size:12px; color:#888; margin-top:3px;">${escapeHtml(row.email || 'Không có email')}</div></td>
                <td>
                    <div class="action-buttons">
                        ${actions}
                    </div>
                </td>
            </tr>`;
    });
    tbody.innerHTML = html;
}

function updateDeleteBtn() {
    const c = document.querySelectorAll('.user-check:checked').length;
    const btn = document.getElementById('btnDeleteSelected');
    if (!btn) return;
    
    document.getElementById('checkAll').checked = (document.querySelectorAll('.user-check:not([disabled])').length > 0 && c === document.querySelectorAll('.user-check:not([disabled])').length);

    btn.innerHTML = `🗑️ Xoá ${c} mục`;
    btn.disabled = c === 0;
    if (c > 0) btn.classList.remove('btn-disabled'); 
    else btn.classList.add('btn-disabled');
}

async function deleteUser(id) {
    if (!confirm('Bạn có chắc chắn muốn xoá người dùng này?')) return;
    
    try {
        const res = await fetch(BASE_URL + 'admin/users/delete/' + id, {method:'DELETE'});
        const result = await res.json();
        
        if (result.status === 'success') {
            showNotification('Đã xoá người dùng thành công!', 'success');
            loadUsers(); 
        } else {
            showNotification(result.message, 'error');
        }
    } catch(e) {
        showNotification('Lỗi kết nối', 'error');
    }
}

async function deleteMultiple() {
    const cbs = Array.from(document.querySelectorAll('.user-check:checked')).map(cb => cb.value);
    if (cbs.length == 0 || !confirm(`Bạn có chắc chắn muốn xoá ${cbs.length} người dùng đã chọn?`)) return;

    try {
        const res = await fetch(BASE_URL + 'admin/users/delete', {
            method: 'DELETE', headers: {'Content-Type':'application/json'}, body: JSON.stringify({ids: cbs})
        });
        const result = await res.json(); 
        
        if (result.status === 'success') {
            showNotification('Đã xoá các người dùng thành công!', 'success');
            loadUsers(); 
        } else {
            showNotification(result.message, 'error');
        }
    } catch(e) {
        showNotification('Lỗi kết nối', 'error');
    }
}

// ---------- API CỦA TRANG THÊM/SỬA ----------
async function initUserForm() {
    try {
        const resItem = await fetch(BASE_URL + 'admin/users/show/' + USER_ID);
        const rItem = await resItem.json();
        
        if (rItem.status === 'success') {
            document.getElementById('username').value = rItem.data.username;
            document.getElementById('hoten').value = rItem.data.hoten;
            document.getElementById('email').value = rItem.data.email || '';
            document.getElementById('role').value = rItem.data.role;

            const currentUser = await fetch(BASE_URL + 'admin/users/data?keyword=');
            const dataResult = await currentUser.json();
            
            if (dataResult.status === 'success') {
                if (dataResult.auth.userId == rItem.data.id) {
                    document.getElementById('adminWarning').style.display = 'block';
                }
            }

            const loadingMsg = document.getElementById('loadingMsg');
            if (loadingMsg) loadingMsg.style.display = 'none';
            document.getElementById('editForm').style.display = 'block';
        } else {
            alert(rItem.message); 
            window.location.href = BASE_URL + 'admin/users';
            return;
        }
    } catch(e) {
        alert('Lỗi API lấy dữ liệu Edit!');
    }
}

async function submitUserForm() {
    const isEdit = (typeof USER_ID !== 'undefined' && USER_ID > 0);
    
    const data = {
        hoten: document.getElementById('hoten').value,
        email: document.getElementById('email').value,
        role: document.getElementById('role').value
    };

    const passwordVal = document.getElementById('password').value;
    
    if (!isEdit) {
        data.username = document.getElementById('username').value;
        data.password = passwordVal;
    } else {
        if (passwordVal.trim() !== '') {
            data.password = passwordVal;
        }
    }

    try {
        let endpoint = BASE_URL + 'admin/users/store';
        let method = 'POST';

        if (isEdit) {
            endpoint = BASE_URL + 'admin/users/update/' + USER_ID;
            method = 'PUT';
        }

        const res = await fetch(endpoint, {
            method: method,
            headers: {'Content-Type':'application/json'},
            body: JSON.stringify(data)
        });
        const r = await res.json();
        
        if (r.status === 'success') {
             window.location.href = BASE_URL + 'admin/users?msg=' + (isEdit ? 'updated' : 'added');
        } else {
            showNotification(r.message, 'error'); 
        }
    } catch(e) {
        showNotification('Lỗi kết nối Server', 'error');
    }
}

function escapeHtml(unsafe) { 
    return (unsafe||'').toString().replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;"); 
}
