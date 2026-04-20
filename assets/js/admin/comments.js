document.addEventListener('DOMContentLoaded', () => {

    const apiTableBody = document.getElementById('apiTableBody');
    if (apiTableBody) {
        // We are on list.php
        catsRendered = false;
        loadList();

        const searchCommentForm = document.getElementById('searchCommentForm');
        if (searchCommentForm) {
            searchCommentForm.addEventListener('submit', (e) => {
                e.preventDefault();
                loadList();
            });
        }
        
        const catIdSelect = document.getElementById('catId');
        if (catIdSelect) {
            catIdSelect.addEventListener('change', loadList);
        }

        const btnRefreshComments = document.getElementById('btnRefreshComments');
        if (btnRefreshComments) {
            btnRefreshComments.addEventListener('click', () => {
                const keywordEl = document.getElementById('keyword');
                if (keywordEl) keywordEl.value = '';
                if (catIdSelect) catIdSelect.value = 0;
                loadList();
            });
        }
    }

    const apiCommentsBody = document.getElementById('apiCommentsBody');
    if (apiCommentsBody) {
        // We are on detail.php
        if (typeof newsId !== 'undefined' && newsId > 0) {
            loadComments();
        }
        
        apiCommentsBody.addEventListener('click', (e) => {
            const deleteBtn = e.target.closest('.btn-delete');
            if (deleteBtn) {
                const id = deleteBtn.getAttribute('data-id');
                if (id) deleteComment(id);
            }
        });
    }

});

// ---------------- LIST LOGIC ----------------
let catsRendered = false;

async function loadList() {
    const keywordEl = document.getElementById('keyword');
    const q = keywordEl ? keywordEl.value.trim() : '';
    const catIdEl = document.getElementById('catId');
    const catId = catIdEl ? catIdEl.value : 0;
    
    const tbody = document.getElementById('apiTableBody');
    if (!tbody) return;
    
    tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 40px;">Đang tải...</td></tr>';

    try {
        let url = BASE_URL + 'api/comments?a=1';
        if (catId > 0) url += '&cat_id=' + catId;
        if (q) url += '&q=' + encodeURIComponent(q);

        const response = await fetch(url);
        const result = await response.json();

        if (result.status === 'success') {
            const data = result.data;

            if (!catsRendered && catIdEl) {
                let catHtml = '<option value="0">-- Tất cả Chuyên mục --</option>';
                data.categories.forEach(c => {
                    catHtml += `<option value="${c.id}">📁 ${escapeHtml(c.name)}</option>`;
                });
                catIdEl.innerHTML = catHtml;
                catIdEl.value = catId;
                catsRendered = true;
            }

            if (data.news.length === 0) {
                tbody.innerHTML = `<tr><td colspan="6" style="text-align:center; padding:40px; color:#666;">
                    ${data.isEditor ? 'Không có bài viết nào thuộc danh mục bạn quản lý.' : 'Không tìm thấy dữ liệu.'}
                </td></tr>`;
                return;
            }

            let html = '';
            data.news.forEach(row => {
                let catDisplay = '';
                if (row.parent_name) {
                    catDisplay = `<span style="font-size: 11px; color: #888;">${escapeHtml(row.parent_name)} ></span><br>`;
                    catDisplay += `<span class="status-badge" style="background: #eef2f5; color: #444;">${escapeHtml(row.cat_name)}</span>`;
                } else {
                    catDisplay = `<span class="status-badge" style="background: #e3f2fd; color: #0d47a1;">${escapeHtml(row.cat_name || 'Chưa phân loại')}</span>`;
                }

                let cmtDisplay = row.total_comments > 0
                    ? `<span style="display:inline-block; padding: 4px 10px; background: #ffebee; color: #c62828; border-radius: 20px; font-weight: bold;">${row.total_comments}</span>`
                    : `<span style="color: #ccc;">0</span>`;

                let actionDisplay = row.total_comments > 0
                    ? `<a class="btn btn-view" href="${BASE_URL}admin/comments/detail/${row.id}">💬 Chi tiết</a>`
                    : `<button class="btn btn-disabled" disabled>Trống</button>`;

                let dateStr = row.ngaydang ? new Date(row.ngaydang).toLocaleDateString('vi-VN') : '';

                html += `
                <tr>
                    <td>${row.id}</td>
                    <td>
                        <a href="${BASE_URL}news/detail/${row.id}" target="_blank" style="text-decoration: none; color: #333; font-weight: 500;">
                            ${escapeHtml(row.tieude.length > 60 ? row.tieude.substring(0, 60) + '...' : row.tieude)}
                        </a><br>
                        <small style="color: #999;">#${row.id}</small>
                    </td>
                    <td>${catDisplay}</td>
                    <td>${dateStr}</td>
                    <td style="text-align: center;">${cmtDisplay}</td>
                    <td>${actionDisplay}</td>
                </tr>`;
            });
            tbody.innerHTML = html;
        } else {
            tbody.innerHTML = `<tr><td colspan="6">${result.message}</td></tr>`;
        }
    } catch(e) {
        tbody.innerHTML = '<tr><td colspan="6">Lỗi kết nối API Server</td></tr>';
    }
}


// ---------------- DETAIL LOGIC ----------------

async function loadComments() {
    const tbody = document.getElementById('apiCommentsBody');
    if (!tbody) return;

    try {
        const response = await fetch(BASE_URL + 'api/comments/' + newsId);
        const result = await response.json();

        if (result.status === 'success') {
            const newsTitleEl = document.getElementById('newsTitle');
            if (newsTitleEl) {
                newsTitleEl.innerHTML = 'Bài viết: ' + escapeHtml(result.data.news_title);
            }

            const comments = result.data.comments;
            if (comments.length === 0) {
                tbody.innerHTML = `<tr><td colspan="6" style="text-align: center; color: #666; padding: 30px;">Chưa có bình luận nào hoặc đã bị xoá sạch.</td></tr>`;
                return;
            }

            let html = '';
            comments.forEach(row => {
                let userDisplay = escapeHtml(row.ten_nguoi_binh || 'Ẩn danh');
                if (row.user_id) userDisplay += `<br><small>User ID: ${row.user_id}</small>`;

                let typeDisplay = row.parent_id ? 'Trả lời' : 'Gốc';

                let dateObj = new Date(row.create_at);
                let pDate = ("0"+dateObj.getDate()).slice(-2) + '/' + ("0"+(dateObj.getMonth()+1)).slice(-2) + '/' + dateObj.getFullYear() + ' ' + ("0"+dateObj.getHours()).slice(-2) + ':' + ("0"+dateObj.getMinutes()).slice(-2);

                html += `
                <tr class="${row.parent_id ? 'comment-child' : ''}">
                    <td>${row.id}</td>
                    <td>${userDisplay}</td>
                    <td>${escapeHtml(row.noidung)}</td>
                    <td>${typeDisplay}</td>
                    <td>${pDate}</td>
                    <td>
                        <button type="button" class="btn btn-delete" style="border:none; cursor:pointer;" data-id="${row.id}">
                            🗑️ Xoá
                        </button>
                    </td>
                </tr>`;
            });
            tbody.innerHTML = html;
        } else {
            tbody.innerHTML = `<tr><td colspan="6" style="color:red; text-align:center;">${result.message}</td></tr>`;
            const newsTitleEl = document.getElementById('newsTitle');
            if (newsTitleEl) newsTitleEl.innerHTML = 'LỖI TẢI DỮ LIỆU';
        }
    } catch(e) {
        tbody.innerHTML = '<tr><td colspan="6">Lỗi kết nối API Server</td></tr>';
    }
}

async function deleteComment(id) {
    if (!confirm('Bạn có chắc chắn muốn xoá bình luận này?')) return;
    try {
        const res = await fetch(BASE_URL + 'api/comments/' + id, { method: 'DELETE' });
        const result = await res.json();
        if (result.status === 'success') {
            const m = document.getElementById('status-msg');
            if (m) {
                m.style.display = 'block'; 
                m.innerHTML = '✅ Đã xoá bình luận thành công!';
                setTimeout(() => m.style.display = 'none', 3000);
            }
            loadComments();
        } else {
            alert('Lỗi: ' + result.message);
        }
    } catch(e) { alert('Lỗi truy xuất API'); }
}

function escapeHtml(unsafe) { 
    return (unsafe||'').toString().replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;"); 
}
