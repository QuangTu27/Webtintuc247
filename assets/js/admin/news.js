let canDeleteAuth = false;
let isAdminOrEditorAuth = false;
let currentUserIdAuth = 0;

document.addEventListener('DOMContentLoaded', () => {

    // --- TRANG LIST ---
    const apiTableBody = document.getElementById('apiTableBody');
    if (apiTableBody) {
        loadNews();

        const filterCategory = document.getElementById('filterCategory');
        if (filterCategory) {
            filterCategory.addEventListener('change', loadNews);
        }

        const btnFilter = document.getElementById('btnFilter');
        if (btnFilter) {
            btnFilter.addEventListener('click', loadNews);
        }

        const btnResetFilter = document.getElementById('btnResetFilter');
        if (btnResetFilter) {
            btnResetFilter.addEventListener('click', () => {
                const fc = document.getElementById('filterCategory');
                if (fc) fc.value = 0;
                loadNews();
            });
        }

        const btnDeleteSelected = document.getElementById('btnDeleteSelected');
        if (btnDeleteSelected) {
            btnDeleteSelected.addEventListener('click', deleteMultiple);
        }

        const checkAll = document.getElementById('checkAll');
        if (checkAll) {
            checkAll.addEventListener('change', function() {
                if (!isAdminOrEditorAuth) return;
                document.querySelectorAll('.news-check').forEach(cb => cb.checked = this.checked);
                updateDeleteBtn();
            });
        }
    }


    // --- TRANG ADD ---
    const addNewsForm = document.getElementById('addNewsForm');
    if (addNewsForm) {
        initAddForm();
        addNewsForm.addEventListener('submit', submitAddForm);
    }

    // --- TRANG EDIT ---
    const editNewsForm = document.getElementById('editNewsForm');
    if (editNewsForm) {
        if (typeof newsId === 'undefined' || !newsId) {
            window.location.href = BASE_URL + 'admin/news';
        } else {
            initEditForm();
            editNewsForm.addEventListener('submit', submitEditForm);
        }
    }

});


// ---------------- LIST LOGIC ----------------
async function loadNews() {
    const filterId = document.getElementById('filterCategory') ? document.getElementById('filterCategory').value : 0;
    const tbody = document.getElementById('apiTableBody');
    if (!tbody) return;

    tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 40px;">Đang tải dữ liệu...</td></tr>';

    try {
        const response = await fetch(BASE_URL + 'api/news?category_id=' + filterId);
        const result = await response.json();

        const filterCatElement = document.getElementById('filterCategory');
        if (filterId == 0 && filterCatElement && filterCatElement.options.length <= 1) {
            const resCat = await fetch(BASE_URL + 'api/news/formdata');
            const dataCat = await resCat.json();
            if (dataCat.status === 'success') {
                let filterHtml = '<option value="0">-- Tất cả danh mục --</option>';
                dataCat.data.categories.forEach(c => {
                    let name = c.parent_id != 0 ? `${c.parent_name} > ${c.name}` : c.name;
                    filterHtml += `<option value="${c.id}">${name}</option>`;
                });
                filterCatElement.innerHTML = filterHtml;
            }
        }

        if (result.status === 'success') {
            isAdminOrEditorAuth = result.auth.isAdminOrEditor;
            currentUserIdAuth = result.auth.userId;
            const checkAllEl = document.getElementById('checkAll');
            if (checkAllEl) checkAllEl.disabled = !isAdminOrEditorAuth;
            
            renderTable(result.data, result.auth);
        } else {
            tbody.innerHTML = `<tr><td colspan="7" style="color:red; text-align:center;">${result.message}</td></tr>`;
        }
    } catch(e) {
        tbody.innerHTML = '<tr><td colspan="7" style="color:red; text-align:center;">Lỗi mạng!</td></tr>';
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
        tbody.innerHTML = `<tr><td colspan="7" style="text-align:center; padding:30px; color:#999;">Không có bài viết nào.</td></tr>`;
        return;
    }

    const stt_map = {
        'ban_nhap': {text: '📝 Nháp', color: '#6c757d', bg: '#e2e3e5'},
        'cho_duyet': {text: '⏳ Chờ duyệt', color: '#856404', bg: '#fff3cd'},
        'da_dang': {text: '✅ Đã đăng', color: '#155724', bg: '#d4edda'},
        'bi_tu_choi': {text: '❌ Từ chối', color: '#721c24', bg: '#f8d7da'}
    };

    let html = '';
    data.forEach(row => {
        let isMyPost = (row.author_id == auth.userId);
        let canAction = (auth.isAdminOrEditor || isMyPost);
        let stt = stt_map[row.trangthai] || stt_map['ban_nhap'];

        let actionsHtml = '';
        if (auth.isAdminOrEditor) {
            if (row.trangthai == 'cho_duyet' || row.trangthai == 'ban_nhap') {
                actionsHtml += `<button type="button" class="btn-icon btn-approve" title="Duyệt bài này" onclick="updateStatus(${row.id}, 'approve')">✅</button> `;
            } else if (row.trangthai == 'da_dang') {
                actionsHtml += `<button type="button" class="btn-icon btn-hide" title="Gỡ bài" onclick="updateStatus(${row.id}, 'hide')">⛔</button> `;
            }
        }
        if (canAction) {
            actionsHtml += `<a href="${BASE_URL}admin/news/edit/${row.id}" class="btn-icon btn-edit" title="Sửa">✏️</a> `;
            actionsHtml += `<button type="button" class="btn-icon btn-delete" title="Xóa" style="background:#f8d7da;" onclick="deleteNews(${row.id})">🗑️</button>`;
        }

        let date = new Date(row.ngaydang).toLocaleString('vi-VN');

        html += `
            <tr>
                <td><input type="checkbox" name="ids[]" class="news-check" value="${row.id}" onchange="updateDeleteBtn()" ${!auth.isAdminOrEditor ? 'disabled':''}></td>
                <td>${row.id}</td>
                <td><img src="${BASE_URL}assets/images/news/${row.hinhanh}" style="width: 70px; height: 50px; object-fit: cover; border-radius: 4px;" onerror="this.src='${BASE_URL}assets/images/default_news.png'"></td>
                <td>
                    <strong style="font-size: 14px; color: #333; display: block; margin-bottom: 5px;">${row.tieude}</strong>
                    <div style="font-size: 12px; color: #888;">
                        <span>✍️ ${row.author_name || 'Ẩn danh'}</span> | <span>📅 ${date}</span>
                    </div>
                </td>
                <td><span class="badge badge-info" style="background:#e3f2fd; color:#0d47a1; padding:3px 8px; border-radius:4px">${row.category_name}</span></td>
                <td><span class="status-badge" style="background:${stt.bg}; color:${stt.color};">${stt.text}</span></td>
                <td><div class="action-buttons" style="display:flex; gap: 5px;">${actionsHtml}</div></td>
            </tr>`;
    });
    tbody.innerHTML = html;
}

// Chú ý: Vì HTML gọi qua onclick truyền hàm toàn cục này nên ta để nó ở scope public
window.updateStatus = async function(id, action) {
    if (!confirm('Xác nhận đổi trạng thái?')) return;
    const body = new FormData();
    body.append('id', id);
    body.append('action', 'update_status');
    body.append('status_action', action);
    const res = await fetch(BASE_URL + 'api/news/status', { method: 'POST', body: body });
    const result = await res.json();
    if (result.status === 'success') { 
        showMsg('Cập nhật trạng thái thành công!'); 
        loadNews(); 
    }
    else alert('Lỗi: ' + result.message);
}

window.deleteNews = async function(id) {
    if (!confirm('Xác nhận xoá bài viết?')) return;
    const res = await fetch(BASE_URL + 'api/news/' + id, {method: 'DELETE'});
    const result = await res.json();
    if (result.status === 'success') { 
        showMsg('Đã xoá bài viết.'); 
        loadNews(); 
    }
    else alert('Lỗi: ' + result.message);
}

async function deleteMultiple() {
    if (!isAdminOrEditorAuth) return;
    const cbs = Array.from(document.querySelectorAll('.news-check:checked')).map(cb => cb.value);
    if (cbs.length == 0 || !confirm('Xác nhận xoá các bài đã chọn?')) return;
    const res = await fetch(BASE_URL + 'api/news', {
        method: 'DELETE', headers: {'Content-Type':'application/json'}, body: JSON.stringify({ids: cbs})
    });
    const result = await res.json();
    if (result.status === 'success') { 
        showMsg('Xoá thành công!'); 
        loadNews(); 
    }
    else alert('Lỗi: ' + result.message);
}

window.updateDeleteBtn = function() {
    if (!isAdminOrEditorAuth) return;
    const cbs = document.querySelectorAll('.news-check:checked');
    const c = cbs ? cbs.length : 0;
    const btn = document.getElementById('btnDeleteSelected');
    if (!btn) return;
    btn.innerHTML = `🗑️ Xoá ${c} bài`;
    btn.disabled = c === 0;
    if (c > 0) btn.classList.remove('btn-disabled'); else btn.classList.add('btn-disabled');
}

function showMsg(msg) {
    const box = document.getElementById('status-msg');
    if (!box) return;
    box.innerHTML = `✅ ${msg}`;
    box.style.display = 'block';
    setTimeout(() => box.style.display = 'none', 3000);
}


// ---------------- ADD LOGIC ----------------
async function initAddForm() {
    if (typeof CKEDITOR !== 'undefined') {
        CKEDITOR.replace('editor', { height: 400, versionCheck: false, allowedContent: true });
    }
    
    try {
        const res = await fetch(BASE_URL + 'api/news/formdata');
        const data = await res.json();

        if (data.status === 'success') {
            let catHtml = '<option value="">-- Chọn danh mục --</option>';
            data.data.categories.forEach(c => {
                let name = c.parent_id != 0 ? `${c.parent_name} > ${c.name}` : c.name;
                catHtml += `<option value="${c.id}">${name}</option>`;
            });
            const danhmucSel = document.getElementById('danhmuc');
            if (danhmucSel) danhmucSel.innerHTML = catHtml;

            const sel = document.getElementById('trangthai');
            if (sel) {
                if (data.auth.canPublish) {
                    sel.innerHTML = `
                        <option value="da_dang">✅ Đăng ngay</option>
                        <option value="cho_duyet">⏳ Chờ duyệt</option>
                        <option value="ban_nhap">📝 Lưu bản nháp</option>`;
                } else {
                    sel.innerHTML = `<option value="cho_duyet" selected>⏳ Gửi chờ duyệt (Không có quyền đăng ngay)</option>`;
                    sel.disabled = true;
                    sel.style.background = '#e9ecef';
                    const hint = document.getElementById('status-hint');
                    if(hint) hint.style.display = 'block';
                }
            }
        } else {
            alert("Lỗi tải form: " + data.message);
        }
    } catch(e) {
        console.error(e);
    }
}

async function submitAddForm(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSubmit');
    if(btn) {
        btn.disabled = true;
        btn.innerHTML = 'Đang lưu...';
    }

    const formData = new FormData(document.getElementById('addNewsForm'));
    if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances.editor) {
        formData.set('noidung', CKEDITOR.instances.editor.getData());
    }
    
    const trangthaiEl = document.getElementById('trangthai');
    if (trangthaiEl && !trangthaiEl.disabled) {
        formData.set('trangthai', trangthaiEl.value);
    } else {
        formData.set('trangthai', 'cho_duyet');
    }

    try {
        const res = await fetch(BASE_URL + 'api/news', { method: 'POST', body: formData });
        const result = await res.json();
        if (result.status === 'success') {
            window.location.href = BASE_URL + 'admin/news';
        } else {
            const box = document.getElementById('status-msg');
            if(box) {
                box.innerHTML = 'Lỗi: ' + result.message;
                box.style.display = 'block';
            }
            if(btn) {
                btn.disabled = false;
                btn.innerHTML = '💾 Lưu bài viết';
            }
        }
    } catch(err) {
        alert('Lỗi kết nối!');
        if(btn) {
            btn.disabled = false;
            btn.innerHTML = '💾 Lưu bài viết';
        }
    }
}


// ---------------- EDIT LOGIC ----------------
async function initEditForm() {
    try {
        const resCat = await fetch(BASE_URL + 'api/news/formdata');
        const dataCat = await resCat.json();

        const resPost = await fetch(BASE_URL + 'api/news/' + newsId);
        const dataPost = await resPost.json();

        if (dataPost.status === 'success' && dataCat.status === 'success') {
            const post = dataPost.data;
            const auth = dataCat.auth;

            if (!auth.canPublish && auth.userId != post.author_id) {
                alert('Bạn không có quyền sửa bài viết này!');
                window.location.href = BASE_URL + 'admin/news';
                return;
            }

            let catHtml = '';
            dataCat.data.categories.forEach(c => {
                let name = c.parent_id != 0 ? `${c.parent_name} > ${c.name}` : c.name;
                let selected = (c.id == post.category_id) ? 'selected' : '';
                catHtml += `<option value="${c.id}" ${selected}>${name}</option>`;
            });
            const danhmucEl = document.getElementById('danhmuc');
            if (danhmucEl) danhmucEl.innerHTML = catHtml;

            const newsIdEl = document.getElementById('news_id');
            if (newsIdEl) newsIdEl.value = post.id;
            
            const tieudeEl = document.getElementById('tieude');
            if (tieudeEl) tieudeEl.value = post.tieude;
            
            const tomtatEl = document.getElementById('tomtat');
            if (tomtatEl) tomtatEl.value = post.tomtat;
            
            const curImgEl = document.getElementById('current_img');
            if (curImgEl) curImgEl.src = BASE_URL + 'assets/images/news/' + post.hinhanh;

            const loadingEl = document.getElementById('loading');
            if (loadingEl) loadingEl.style.display = 'none';
            
            const editFormEl = document.getElementById('editNewsForm');
            if (editFormEl) editFormEl.style.display = 'block';

            if (typeof CKEDITOR !== 'undefined') {
                CKEDITOR.replace('editor', { height: 400, versionCheck: false, allowedContent: true });
                CKEDITOR.instances.editor.on('instanceReady', function() {
                    CKEDITOR.instances.editor.setData(post.noidung);
                });
            }

            const selStart = document.getElementById('trangthai');
            if (selStart) {
                if (auth.canPublish) {
                    selStart.innerHTML = `
                        <option value="da_dang" ${post.trangthai=='da_dang'?'selected':''}>✅ Đã đăng</option>
                        <option value="cho_duyet" ${post.trangthai=='cho_duyet'?'selected':''}>⏳ Chờ duyệt</option>
                        <option value="ban_nhap" ${post.trangthai=='ban_nhap'?'selected':''}>📝 Bản nháp</option>`;
                } else {
                    selStart.innerHTML = `<option value="cho_duyet" selected>⏳ Gửi chờ duyệt lại</option>`;
                    selStart.disabled = true;
                    selStart.style.background = '#e9ecef';
                }
            }
        } else {
            alert("Lỗi tải dữ liệu: " + (dataPost.message || dataCat.message));
            window.location.href = BASE_URL + 'admin/news';
        }
    } catch(e) {
        const loadingEl = document.getElementById('loading');
        if (loadingEl) loadingEl.innerHTML = "Lỗi kết nối hoặc xử lý dữ liệu!";
    }
}

async function submitEditForm(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSubmit');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = 'Đang lưu...';
    }

    const formData = new FormData(document.getElementById('editNewsForm'));
    if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances.editor) {
        formData.set('noidung', CKEDITOR.instances.editor.getData());
    }
    
    const trangthaiEl = document.getElementById('trangthai');
    if (trangthaiEl && !trangthaiEl.disabled) {
        formData.set('trangthai', trangthaiEl.value);
    } else {
        formData.set('trangthai', 'cho_duyet');
    }

    try {
        const res = await fetch(BASE_URL + 'api/news/' + newsId, { method: 'POST', body: formData });
        const result = await res.json();
        if (result.status === 'success') {
            window.location.href = BASE_URL + 'admin/news';
        } else {
            const box = document.getElementById('status-msg');
            if (box) {
                box.innerHTML = 'Lỗi: ' + result.message;
                box.style.display = 'block';
            }
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '💾 Cập nhật';
            }
        }
    } catch(err) {
        alert('Lỗi kết nối!');
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '💾 Cập nhật';
        }
    }
}
