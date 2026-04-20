let canDeleteAuth = false;
let canEditAuth = false;
let formIsAdmin = false;

// HÀM TOÀN CỤC
function showNotification(text, type = 'success') {
    let msgDiv = document.getElementById('status-msg');
    
    if (!msgDiv) {
        msgDiv = document.createElement('div');
        msgDiv.id = 'status-msg';
        msgDiv.style.cssText = 'padding: 15px; margin-bottom: 20px; border-radius: 4px; color: white; font-weight: bold; transition: opacity 0.5s;';
        
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

// GẮN SỰ KIỆN KHI TRANG VỪA LOAD XONG
document.addEventListener('DOMContentLoaded', () => {
    
    // Đọc thanh URL xem có thông báo thêm/sửa không
    const urlParams = new URLSearchParams(window.location.search);
    const msg = urlParams.get('msg'); 

    if (msg) {
        if (msg === 'added') showNotification('Thêm danh mục mới thành công!', 'success');
        else if (msg === 'updated') showNotification('Cập nhật danh mục thành công!', 'success');
        else if (msg === 'error') showNotification('Có lỗi xảy ra, vui lòng thử lại!', 'error');

        window.history.replaceState({}, document.title, window.location.pathname);
    }

    //Các sự kiện cho bảng danh sách
    const tbody = document.getElementById('apiTableBody');
    if (tbody) {
        loadCategories();

        const filterSelect = document.getElementById('filterId');
        if (filterSelect) filterSelect.addEventListener('change', loadCategories);

        const btnSearch = document.getElementById('btnSearch');
        if (btnSearch) btnSearch.addEventListener('click', loadCategories);

        const inputKeyword = document.getElementById('keyword');
        if (inputKeyword) {
            inputKeyword.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') loadCategories();
            });
        }

        const checkAll = document.getElementById('checkAll');
        if (checkAll) {
            checkAll.addEventListener('change', function() {
                if (!canDeleteAuth) return;
                document.querySelectorAll('.cat-check').forEach(cb => cb.checked = this.checked);
                updateDeleteBtn();
            });
        }

        const btnDeleteSelected = document.getElementById('btnDeleteSelected');
        if (btnDeleteSelected) btnDeleteSelected.addEventListener('click', deleteMultiple);

        tbody.addEventListener('change', (e) => {
            if (e.target.classList.contains('cat-check')) updateDeleteBtn();
        });

        tbody.addEventListener('click', (e) => {
            if (e.target.classList.contains('btn-delete-item')) {
                const id = e.target.getAttribute('data-id');
                if (id) deleteCat(id);
            }
        });
    }

    // --- (ADD/EDIT FORM) Các sự kiện cho Form ---
    const categoryForm = document.getElementById('categoryForm');
    if (categoryForm) {
        initCategoryForm();
        categoryForm.addEventListener('submit', (e) => {
            e.preventDefault();
            submitCategoryForm();
        });
    }
});

// CÁC HÀM XỬ LÝ API 
async function loadCategories() {
    const filterSelect = document.getElementById('filterId');
    const filterId = filterSelect ? filterSelect.value : 0;
    
    const keywordInput = document.getElementById('keyword');
    const keyword = keywordInput ? encodeURIComponent(keywordInput.value.trim()) : '';

    const tbody = document.getElementById('apiTableBody');
    if (!tbody) return;
    
    tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 40px;">Đang tải dữ liệu...</td></tr>';

    try {
        const response = await fetch(BASE_URL + `api/categories?filter_id=${filterId}&keyword=${keyword}`);
        const result = await response.json();

        if (result.status === 'success') {
            canDeleteAuth = result.auth.canDelete;
            canEditAuth = result.auth.canEdit;

            const btnAdd = document.getElementById('btnAdd');
            if (result.auth.canAdd && btnAdd) {
                btnAdd.classList.remove('btn-disabled');
                btnAdd.style.pointerEvents = 'auto';
            }

            const checkAll = document.getElementById('checkAll');
            if (checkAll) checkAll.disabled = !canDeleteAuth;

            if (filterId == 0) {
                let filterHtml = '<option value="0">-- Tất cả danh mục --</option>';
                result.parents.forEach(p => {
                    filterHtml += `<option value="${p.id}">📁 ${escapeHtml(p.name)}</option>`;
                });
                const filterSelect = document.getElementById('filterId');
                if (filterSelect) filterSelect.innerHTML = filterHtml;
            }

            renderTable(result.data, result.auth);
        } else {
            tbody.innerHTML = `<tr><td colspan="6" style="color:red; text-align:center;">${result.message}</td></tr>`;
        }
    } catch {
        tbody.innerHTML = '<tr><td colspan="6" style="color:red; text-align:center;">Lỗi mạng!</td></tr>';
    }
}

function renderTable(data, auth) {
    const tbody = document.getElementById('apiTableBody');
    if (!tbody) return;
    
    tbody.innerHTML = '';
    const checkAll = document.getElementById('checkAll');
    if (checkAll) checkAll.checked = false;
    updateDeleteBtn();

    if (data.length === 0) {
        tbody.innerHTML = `<tr><td colspan="6" style="text-align:center; padding:30px; color:#999;">Không có danh mục nào.</td></tr>`;
        return;
    }

    let html = '';
    data.forEach(row => {
        let nameHtml = row.parent_id != 0
            ? `<span style="color:#999; margin-left: 20px;">└──</span> ${escapeHtml(row.name)}`
            : `<strong style="color:#d32f2f; font-size: 15px;">${escapeHtml(row.name)}</strong>`;

        let capdoHtml = row.parent_id == 0
            ? `<span class="status-badge" style="background:#e3f2fd; color:#0d47a1;">Gốc</span>`
            : `<span class="status-badge" style="background:#f5f5f5; color:#666;">Con: <strong>${escapeHtml(row.parent_name)}</strong></span>`;

        let managerHtml = '';
        if (row.manager_name) managerHtml = `<span style="color:#2e7d32; font-weight:600">${escapeHtml(row.manager_name)}</span>`;
        else if (row.parent_manager_name) managerHtml = `<span style="color:#555;">${escapeHtml(row.parent_manager_name)}</span><div style="font-size:11px; color:#999; font-style:italic;">(Theo danh mục cha)</div>`;
        else managerHtml = `<i style="color:#ccc; font-size:12px;">Chưa phân công</i>`;

        html += `
            <tr>
                <td><input type="checkbox" name="ids[]" class="cat-check" value="${row.id}" ${!auth.canDelete ? 'disabled':''}></td>
                <td>${row.id}</td>
                <td style="font-weight: 500;">${nameHtml}</td>
                <td>${capdoHtml}</td>
                <td>${managerHtml}</td>
                <td>
                    <div class="action-buttons">
                        <a class="btn btn-edit ${!auth.canEdit ? 'btn-disabled':''}" href="${auth.canEdit ? BASE_URL+'admin/categories/edit/'+row.id : '#'}">✏️ Sửa</a>
                        <button type="button" class="btn btn-delete btn-delete-item" data-id="${row.id}" ${!auth.canDelete ? 'disabled style="opacity:0.5"':''}>🗑️ Xoá</button>
                    </div>
                </td>
            </tr>`;
    });
    tbody.innerHTML = html;
}

async function deleteCat(id) {
    if (!canDeleteAuth) return;
    if (!confirm('Xác nhận xoá?')) return;
    const res = await fetch(BASE_URL + 'api/categories/' + id, {method:'DELETE'});
    const result = await res.json();
    if (result.status === 'success') {
        showNotification('Đã xoá danh mục thành công!', 'success');
        loadCategories(); 
    } else {
        showNotification(result.message, 'error');
    }
}

async function deleteMultiple() {
    if (!canDeleteAuth) return;
    const cbs = Array.from(document.querySelectorAll('.cat-check:checked')).map(cb => cb.value);
    if (cbs.length == 0 || !confirm('Xác nhận xoá?')) return;

    const res = await fetch(BASE_URL + 'api/categories', {
        method: 'DELETE', headers: {'Content-Type':'application/json'}, body: JSON.stringify({ids: cbs})
    });
    const result = await res.json(); 
    
    if (result.status === 'success') {
        showNotification('Đã xoá các danh mục thành công!', 'success');
        loadCategories(); 
    } else {
        showNotification(result.message, 'error');
    }
}

function updateDeleteBtn() {
    if (!canDeleteAuth) return;
    const c = document.querySelectorAll('.cat-check:checked').length;
    const btn = document.getElementById('btnDeleteSelected');
    if (!btn) return;
    
    btn.innerHTML = `🗑️ Xoá ${c} mục`;
    btn.disabled = c === 0;
    if (c > 0) btn.classList.remove('btn-disabled'); 
    else btn.classList.add('btn-disabled');
}


// --- CÁC HÀM CỦA TRANG THÊM/SỬA FORM ---
async function initCategoryForm() {
    try {
        const resForm = await fetch(BASE_URL + 'api/categories/formdata');
        const rForm = await resForm.json();

        if (rForm.status === 'success') {
            formIsAdmin = rForm.auth.isAdmin;
            let currentParentId = 0;
            let currentManagerId = 0;

            if (CATEGORY_ID > 0) {
                const resItem = await fetch(BASE_URL + 'api/categories/' + CATEGORY_ID);
                const rItem = await resItem.json();
                if (rItem.status === 'success') {
                    document.getElementById('catName').value = rItem.data.name;
                    currentParentId = rItem.data.parent_id;
                    currentManagerId = rItem.data.manager_id;
                } else {
                    alert('Lỗi truy xuất dữ liệu'); 
                    window.location.href = BASE_URL + 'admin/categories';
                    return;
                }
            }

            let phtml = '<option value="0">-- Là danh mục gốc (Không có cha) --</option>';
            rForm.data.parents.forEach(p => {
                if (p.id != CATEGORY_ID) {
                    phtml += `<option value="${p.id}" ${p.id == currentParentId ? 'selected':''}>${escapeHtml(p.name)}</option>`;
                }
            });
            document.getElementById('parentId').innerHTML = phtml;

            const mGroup = document.getElementById('managerGroup');
            if (formIsAdmin) {
                let mhtml = `<label>*Người phụ trách</label><select id="managerId" class="form-control"><option value="0">-- Chưa phân công --</option>`;
                rForm.data.managers.forEach(m => {
                    mhtml += `<option value="${m.id}" ${m.id == currentManagerId ? 'selected':''}>${m.hoten} (${m.username})</option>`;
                });
                mhtml += `</select><small class="form-hint">* Admin phân công ai thì người đó mới được đăng bài vào mục này.</small>`;
                mGroup.innerHTML = mhtml;
            } else if (CATEGORY_ID === 0) {
                mGroup.innerHTML = `
                    <label>*Người phụ trách (Trưởng ban)</label>
                    <input type="text" value="${rForm.auth.userName} (Tự động gán)" disabled style="background-color: #e9ecef;">
                    <input type="hidden" id="managerId" value="${rForm.auth.userId}">
                    <small class="form-hint">* Bạn tạo danh mục này nên bạn sẽ là người quản lý.</small>
                `;
            }

            document.getElementById('loading').style.display = 'none';
            document.getElementById('categoryForm').style.display = 'block';
        }
    } catch(e) {
        alert('Lỗi API khởi tạo Form!');
    }
}

async function submitCategoryForm() {
    const data = {
        name: document.getElementById('catName').value,
        parent_id: document.getElementById('parentId').value
    };
    
    if (document.getElementById('managerId')) {
        data.manager_id = document.getElementById('managerId').value;
    }

    try {
        let endpoint = BASE_URL + 'api/categories';
        let method = 'POST';

        if (CATEGORY_ID > 0) {
            endpoint = BASE_URL + 'api/categories/' + CATEGORY_ID;
            method = 'PUT';
        }

        const res = await fetch(endpoint, {
            method: method,
            headers: {'Content-Type':'application/json'},
            body: JSON.stringify(data)
        });
        const r = await res.json();
        
        if (r.status === 'success') {
            if (typeof CATEGORY_ID !== 'undefined' && CATEGORY_ID > 0) {
                window.location.href = BASE_URL + 'admin/categories?msg=updated';
            } else {
                window.location.href = BASE_URL + 'admin/categories?msg=added';
            }
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