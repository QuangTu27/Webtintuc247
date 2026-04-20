document.addEventListener('DOMContentLoaded', () => {

    // LIST
    const tbody = document.getElementById('apiTableBody');
    if (tbody) {
        loadAds();
        
        const searchForm = document.querySelector('.search-form');
        if (searchForm) {
            searchForm.addEventListener('submit', (e) => {
                e.preventDefault();
                loadAds();
            });
            const btnReset = searchForm.querySelector('.btn-view');
            if (btnReset) {
                btnReset.addEventListener('click', () => {
                    const searchInput = document.getElementById('searchInput');
                    if (searchInput) searchInput.value = '';
                    loadAds();
                });
            }
        }
        
        const btnDeleteSelected = document.getElementById('btnDeleteSelected');
        if (btnDeleteSelected) {
            btnDeleteSelected.addEventListener('click', deleteMultiple);
        }
        
        const checkAll = document.getElementById('checkAll');
        if (checkAll) {
            checkAll.addEventListener('change', function() {
                document.querySelectorAll('.ad-check').forEach(cb => cb.checked = this.checked);
                updateDeleteBtn();
            });
        }
        
        tbody.addEventListener('change', (e) => {
            if (e.target.classList.contains('ad-check')) updateDeleteBtn();
        });
        
        tbody.addEventListener('click', (e) => {
            const deleteBtn = e.target.closest('.btn-delete');
            if (deleteBtn) {
                const id = deleteBtn.getAttribute('data-id');
                if (id) deleteAd(id);
            }
        });
    }

    // ADD 
    const addForm = document.getElementById('addForm');
    if (addForm) {
        addForm.addEventListener('submit', (e) => {
            e.preventDefault();
            submitAdd();
        });
    }

    // EDIT 
    const editForm = document.getElementById('editForm');
    if (editForm) {
        if (typeof adId !== 'undefined' && adId > 0) {
            initEditForm();
        }
        editForm.addEventListener('submit', (e) => {
            e.preventDefault();
            submitEdit();
        });
    }

});

async function loadAds() {
    const searchInput = document.getElementById('searchInput');
    const search = searchInput ? searchInput.value.trim() : '';
    const tbody = document.getElementById('apiTableBody');
    if (!tbody) return;
    
    tbody.innerHTML = '<tr><td colspan="8" style="text-align: center; padding: 40px;">Đang tải...</td></tr>';

    try {
        let url = BASE_URL + 'api/ads';
        if (search) url += '?search=' + encodeURIComponent(search);

        const response = await fetch(url);
        const result = await response.json();

        if (result.status === 'success') {
            const checkAll = document.getElementById('checkAll');
            if (checkAll) checkAll.checked = false;
            updateDeleteBtn();
            
            if (result.data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="8" style="text-align:center; padding:40px; color:#666;">Danh sách quảng cáo trống.</td></tr>`;
                return;
            }

            let html = '';
            result.data.forEach(row => {
                let mediaHtml = row.media_type === 'video'
                    ? `<video width="120" height="auto" autoplay muted loop playsinline style="border-radius: 4px;"><source src="${BASE_URL}assets/images/ads/${row.media_file}" type="video/mp4"></video>`
                    : `<img src="${BASE_URL}assets/images/ads/${row.media_file}" style="width: 120px; height: auto; border-radius: 4px;">`;

                let statusHtml = `<span class="status-badge ${row.status}">${row.status === 'hien' ? 'Hiển thị' : 'Ẩn'}</span>`;

                html += `
                <tr>
                    <td><input type="checkbox" name="ids[]" class="ad-check" value="${row.id}"></td>
                    <td>${row.id}</td>
                    <td>${escapeHtml(row.title)}</td>
                    <td class="ads-media" style="width: 150px;">${mediaHtml}</td>
                    <td><a href="${escapeHtml(row.link)}" target="_blank">Xem link</a></td>
                    <td>${escapeHtml(row.position)}</td>
                    <td>${statusHtml}</td>
                    <td>
                        <div class="action-buttons">
                            <a class="btn btn-edit" href="${BASE_URL}admin/ads/edit/${row.id}">✏️ Sửa</a>
                            <button type="button" class="btn btn-delete" style="border:none; cursor:pointer;" data-id="${row.id}">🗑️ Xoá</button>
                        </div>
                    </td>
                </tr>`;
            });
            tbody.innerHTML = html;
        } else {
            tbody.innerHTML = `<tr><td colspan="8">${result.message}</td></tr>`;
        }
    } catch(e) {
        tbody.innerHTML = '<tr><td colspan="8">Lỗi mạng</td></tr>';
    }
}

async function deleteAd(id) {
    if (!confirm('Bạn có chắc muốn xoá quảng cáo này?')) return;
    try {
        const res = await fetch(BASE_URL + 'api/ads/' + id, { method: 'DELETE' });
        const result = await res.json();
        if (result.status === 'success') { 
            showMsg('🗑️ Đã xoá quảng cáo.'); 
            loadAds(); 
        }
        else alert(result.message);
    } catch(e) { alert('Lỗi kết nối Server'); }
}

async function deleteMultiple() {
    const cbs = Array.from(document.querySelectorAll('.ad-check:checked')).map(cb => cb.value);
    if (cbs.length === 0 || !confirm('Xác nhận xoá ' + cbs.length + ' mục?')) return;
    try {
        const res = await fetch(BASE_URL + 'api/ads', {
            method: 'DELETE', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ ids: cbs })
        });
        const result = await res.json();
        if (result.status === 'success') { 
            showMsg('🗑️ Đã xoá các quảng cáo đã chọn.'); 
            loadAds(); 
        } else {
            alert(result.message);
        }
    } catch(e) { alert('Lỗi kết nối Server'); }
}

function updateDeleteBtn() {
    const count = document.querySelectorAll('.ad-check:checked').length;
    const btn = document.getElementById('btnDeleteSelected');
    if (!btn) return;
    btn.innerHTML = `🗑️ Xoá ${count} quảng cáo`;
    btn.disabled = count === 0;
    if (count > 0) btn.classList.remove('btn-disabled'); else btn.classList.add('btn-disabled');
}

function showMsg(str, type = 'success') {
    const d = document.getElementById('status-msg');
    if (!d) return;
    d.innerHTML = str;
    d.className = type === 'success' ? 'alert alert-success' : 'alert alert-warning';
    d.style.display = 'block';
    setTimeout(() => d.style.display = 'none', 3000);
}

function escapeHtml(unsafe) { return (unsafe||'').toString().replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;"); }

async function submitAdd() {
    const formParams = new FormData();
    formParams.append('title', document.getElementById('title').value);
    formParams.append('link', document.getElementById('link').value);
    formParams.append('position', document.getElementById('position').value);
    formParams.append('status', document.getElementById('status').value);

    const fileInput = document.getElementById('media_file');
    if (fileInput && fileInput.files.length > 0) {
        formParams.append('media_file', fileInput.files[0]);
    }

    try {
        const response = await fetch(BASE_URL + 'api/ads', { method: 'POST', body: formParams });
        const result = await response.json();
        if (result.status === 'success') {
            window.location.href = BASE_URL + 'admin/ads';
        } else {
            const m = document.getElementById('status-msg');
            m.style.display = 'block'; m.innerHTML = '❌ ' + result.message;
        }
    } catch(e) {
        alert('Lỗi kết nối Server!');
    }
}

async function initEditForm() {
    try {
        const res = await fetch(BASE_URL + 'api/ads/' + adId);
        const result = await res.json();

        if (result.status === 'success') {
            const ad = result.data;
            document.getElementById('title').value = ad.title;
            document.getElementById('link').value = ad.link;
            document.getElementById('position').value = ad.position;
            document.getElementById('status').value = ad.status;

            const mediaDiv = document.getElementById('currentMedia');
            if (ad.media_type === 'video') {
                mediaDiv.innerHTML = `<video width="200" autoplay muted loop playsinline style="border-radius: 4px; border: 1px solid #ddd;"><source src="${BASE_URL}assets/images/ads/${ad.media_file}" type="video/mp4"></video>`;
            } else {
                mediaDiv.innerHTML = `<img src="${BASE_URL}assets/images/ads/${ad.media_file}" style="max-width: 200px; border-radius: 4px; border: 1px solid #ddd;">`;
            }

            document.getElementById('loading').style.display = 'none';
            document.getElementById('editForm').style.display = 'block';
        } else {
            alert('Lỗi: ' + result.message);
            window.location.href = BASE_URL + 'admin/ads';
        }
    } catch(e) { alert('Lỗi tải dữ liệu'); }
}

async function submitEdit() {
    const formParams = new FormData();
    formParams.append('id', adId);
    formParams.append('title', document.getElementById('title').value);
    formParams.append('link', document.getElementById('link').value);
    formParams.append('position', document.getElementById('position').value);
    formParams.append('status', document.getElementById('status').value);

    const fileInput = document.getElementById('image_file');
    if (fileInput && fileInput.files.length > 0) {
        formParams.append('image_file', fileInput.files[0]);
    }

    try {
        const response = await fetch(BASE_URL + 'api/ads/' + adId, { method: 'POST', body: formParams });
        const result = await response.json();
        if (result.status === 'success') {
            window.location.href = BASE_URL + 'admin/ads';
        } else {
            const m = document.getElementById('status-msg');
            m.style.display = 'block'; m.innerHTML = '❌ ' + result.message;
        }
    } catch(e) {
        alert('Lỗi kết nối Server!');
    }
}
