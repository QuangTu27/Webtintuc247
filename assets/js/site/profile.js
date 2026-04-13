/**
 * assets/js/site/profile.js
 * Xử lý toàn bộ logic trang Hồ sơ cá nhân (Profile) phía site.
 *
 * Phân vùng:
 *  - Bootstrap: DOMContentLoaded -> loadUserInfo() -> renderTab(ACTIVE_TAB)
 *  - renderTab(tab): điều hướng sang render từng tab
 *  - Tab GENERAL: renderGeneral(user)
 *  - Tab COMMENTS: fetchAndRenderComments()
 *  - Tab BOOKMARKS: fetchAndRenderBookmarks()
 *  - Tab HISTORY: fetchAndRenderHistory()
 *  - Helpers: toggleEdit, togglePassword, escapeHtml
 */

document.addEventListener('DOMContentLoaded', async () => {
    // Xác nhận đăng xuất
    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', (e) => {
            if (!confirm('Bạn có chắc muốn đăng xuất?')) e.preventDefault();
        });
    }

    await loadUserInfo();
    renderTab(ACTIVE_TAB);
});

// ========================
//   USER INFO (sidebar)
// ========================
let currentUser = {};

async function loadUserInfo() {
    try {
        const res = await fetch(BASE_URL + 'user/info');
        const result = await res.json();
        if (result.status === 'success') {
            currentUser = result.data;

            // Cập nhật sidebar
            const avatarEl = document.getElementById('sidebar-avatar');
            const nameEl   = document.getElementById('sidebar-username');
            const joinEl   = document.getElementById('sidebar-join');

            if (avatarEl && currentUser.avatar) {
                avatarEl.src = SITE_AVATAR_BASE + currentUser.avatar;
                avatarEl.onerror = () => { avatarEl.src = DEFAULT_AVATAR; };
            }
            if (nameEl) nameEl.innerText = currentUser.username || '';
            if (joinEl && currentUser.created_at) {
                const d = new Date(currentUser.created_at);
                const mm = String(d.getMonth() + 1).padStart(2, '0');
                const yy = d.getFullYear();
                joinEl.innerText = `Tham gia từ ${mm}/${yy}`;
            }
        } else {
            alert('Phiên đăng nhập hết hạn. Vui lòng đăng nhập lại.');
            window.location.href = BASE_URL;
        }
    } catch (e) {
        console.error('Lỗi tải thông tin người dùng:', e);
    }
}

// ========================
//       TAB ROUTING
// ========================
function renderTab(tab) {
    switch (tab) {
        case 'comments':  fetchAndRenderComments();  break;
        case 'bookmarks': fetchAndRenderBookmarks(); break;
        case 'history':   fetchAndRenderHistory();   break;
        case 'general':
        default:          renderGeneral();            break;
    }
}

function setContent(html) {
    const el = document.getElementById('profile-main-content');
    if (el) el.innerHTML = html;
}

function setLoading(msg = 'Đang tải dữ liệu...') {
    setContent(`<div class="loading-profile" style="text-align:center;padding:50px;color:#888;font-style:italic;">${msg}</div>`);
}

// ========================
//     TAB: GENERAL
// ========================
function renderGeneral() {
    const u = currentUser;
    const avatar  = u.avatar ? SITE_AVATAR_BASE + u.avatar : DEFAULT_AVATAR;
    const hoten   = escapeHtml(u.hoten || 'Chưa cập nhật');
    const email   = escapeHtml(u.email || 'Chưa cập nhật email');

    setContent(`
        <h2 class="page-title">Thông tin tài khoản</h2>

        <!-- AVATAR -->
        <div class="info-section">
            <div class="info-row avatar-row" id="view-avatar">
                <div>
                    <span class="info-label">Ảnh đại diện</span>
                    <div><img src="${avatar}" class="avatar-sm" id="current-avatar-preview" onerror="this.src='${DEFAULT_AVATAR}'"></div>
                </div>
                <a href="javascript:void(0)" class="btn-change" id="btn-change-avatar">Thay ảnh đại diện</a>
            </div>
            <div class="edit-container" id="edit-avatar" style="display:none;">
                <div class="mb-10">
                    <span class="info-label">Cập nhật ảnh</span>
                    <span class="btn-close-edit" id="close-avatar">Đóng</span>
                </div>
                <form class="avatar-edit-box" id="form-avatar">
                    <input type="file" name="avatar_file" id="file-upload" accept="image/*" required class="form-control">
                    <button type="submit" class="btn-save">Lưu thay đổi</button>
                </form>
            </div>
        </div>

        <!-- HỌ TÊN -->
        <div class="info-section">
            <div class="info-row" id="view-name">
                <div><span class="info-label">Họ tên</span><span class="info-value" id="display-hoten">${hoten}</span></div>
                <a href="javascript:void(0)" class="btn-change" id="btn-change-name">Thay đổi</a>
            </div>
            <div class="edit-container" id="edit-name" style="display:none;">
                <div class="mb-10">
                    <span class="info-label">Họ tên</span>
                    <span class="btn-close-edit" id="close-name">Đóng</span>
                </div>
                <form class="normal-edit-box" id="form-name">
                    <label class="form-label">Nhập họ tên</label>
                    <input type="text" name="hoten" class="form-control" value="${hoten !== 'Chưa cập nhật' ? hoten : ''}" required>
                    <button type="submit" class="btn-save">Đổi tên</button>
                </form>
            </div>
        </div>

        <!-- EMAIL -->
        <div class="info-section">
            <div class="info-row" id="view-email">
                <div><span class="info-label">Email</span><span class="info-value" id="display-email">${email}</span></div>
                <a href="javascript:void(0)" class="btn-change" id="btn-change-email">Thay đổi</a>
            </div>
            <div class="edit-container" id="edit-email" style="display:none;">
                <div class="mb-10">
                    <span class="info-label">Email</span>
                    <span class="btn-close-edit" id="close-email">Đóng</span>
                </div>
                <form class="normal-edit-box" id="form-email">
                    <label class="form-label">Nhập email mới</label>
                    <input type="email" name="email" class="form-control" value="${email !== 'Chưa cập nhật email' ? email : ''}" required>
                    <button type="submit" class="btn-save">Đổi email</button>
                </form>
            </div>
        </div>

        <!-- MẬT KHẨU -->
        <div class="info-section">
            <div class="info-row" id="view-pass">
                <div><span class="info-label">Mật khẩu</span><span class="info-value">•••••••••••••</span></div>
                <a href="javascript:void(0)" class="btn-change" id="btn-change-pass">Đổi mật khẩu</a>
            </div>
            <div class="edit-container" id="edit-pass" style="display:none;">
                <div class="mb-10">
                    <span class="info-label">Mật khẩu</span>
                    <span class="btn-close-edit" id="close-pass">Đóng</span>
                </div>
                <form class="normal-edit-box" id="form-pass">
                    <label class="form-label">Mật khẩu hiện tại</label>
                    <div class="password-wrapper">
                        <input type="password" id="old_pass" name="old_password" class="form-control" required>
                        <span class="toggle-text" data-target="old_pass">Hiện</span>
                    </div>
                    <label class="form-label">Mật khẩu mới</label>
                    <div class="password-wrapper">
                        <input type="password" id="new_pass" name="new_password" class="form-control" required>
                        <span class="toggle-text" data-target="new_pass">Hiện</span>
                    </div>
                    <label class="form-label">Xác nhận mật khẩu mới</label>
                    <div class="password-wrapper">
                        <input type="password" id="confirm_pass" name="confirm_password" class="form-control" required>
                        <span class="toggle-text" data-target="confirm_pass">Hiện</span>
                    </div>
                    <p id="pass-error" style="color:#dc3545; font-size:13px; display:none;"></p>
                    <button type="submit" class="btn-save">Đổi mật khẩu</button>
                </form>
            </div>
        </div>
    `);

    // Bind toggle-edit buttons
    bindToggleEditBtn('btn-change-avatar', 'close-avatar', 'view-avatar', 'edit-avatar');
    bindToggleEditBtn('btn-change-name',   'close-name',   'view-name',   'edit-name');
    bindToggleEditBtn('btn-change-email',  'close-email',  'view-email',  'edit-email');
    bindToggleEditBtn('btn-change-pass',   'close-pass',   'view-pass',   'edit-pass');

    // Bind toggle-password
    document.querySelectorAll('.toggle-text[data-target]').forEach(span => {
        span.addEventListener('click', () => {
            const input = document.getElementById(span.dataset.target);
            if (!input) return;
            input.type = input.type === 'password' ? 'text' : 'password';
            span.innerText = input.type === 'password' ? 'Hiện' : 'Ẩn';
        });
    });

    // Form Handlers
    const formAvatar = document.getElementById('form-avatar');
    if (formAvatar) {
        formAvatar.addEventListener('submit', async (e) => {
            e.preventDefault();
            const fd = new FormData(formAvatar);
            const btn = formAvatar.querySelector('button[type=submit]');
            btn.disabled = true; btn.innerText = 'Đang tải lên...';
            try {
                const res = await fetch(BASE_URL + 'user/updateAvatar', { method: 'POST', body: fd });
                const result = await res.json();
                if (result.status === 'success') {
                    currentUser.avatar = result.avatar;
                    // Update sidebar + preview
                    const sidebarAvatar = document.getElementById('sidebar-avatar');
                    if (sidebarAvatar) sidebarAvatar.src = SITE_AVATAR_BASE + result.avatar;
                    renderGeneral();
                } else {
                    alert(result.message);
                    btn.disabled = false; btn.innerText = 'Lưu thay đổi';
                }
            } catch (err) { alert('Lỗi mạng!'); btn.disabled = false; btn.innerText = 'Lưu thay đổi'; }
        });
    }

    const formName = document.getElementById('form-name');
    if (formName) {
        formName.addEventListener('submit', async (e) => {
            e.preventDefault();
            const fd = new FormData(formName);
            try {
                const res = await fetch(BASE_URL + 'user/updateName', { method: 'POST', body: fd });
                const result = await res.json();
                alert(result.message);
                if (result.status === 'success') {
                    currentUser.hoten = fd.get('hoten');
                    renderGeneral();
                }
            } catch { alert('Lỗi mạng!'); }
        });
    }

    const formEmail = document.getElementById('form-email');
    if (formEmail) {
        formEmail.addEventListener('submit', async (e) => {
            e.preventDefault();
            const fd = new FormData(formEmail);
            try {
                const res = await fetch(BASE_URL + 'user/updateEmail', { method: 'POST', body: fd });
                const result = await res.json();
                alert(result.message);
                if (result.status === 'success') {
                    currentUser.email = fd.get('email');
                    renderGeneral();
                }
            } catch { alert('Lỗi mạng!'); }
        });
    }

    const formPass = document.getElementById('form-pass');
    if (formPass) {
        formPass.addEventListener('submit', async (e) => {
            e.preventDefault();
            const errEl = document.getElementById('pass-error');
            const newP  = document.getElementById('new_pass').value;
            const conP  = document.getElementById('confirm_pass').value;
            if (newP !== conP) {
                errEl.innerText = 'Mật khẩu mới không khớp'; errEl.style.display = 'block';
                return;
            }
            errEl.style.display = 'none';
            const payload = Object.fromEntries(new FormData(formPass).entries());
            try {
                const res = await fetch(BASE_URL + 'user/changePassword', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const result = await res.json();
                alert(result.message);
                if (result.status === 'success') window.location.href = BASE_URL + 'auth/logout';
            } catch { alert('Lỗi mạng!'); }
        });
    }
}

function bindToggleEditBtn(btnId, closeId, viewId, editId) {
    const btn   = document.getElementById(btnId);
    const close = document.getElementById(closeId);
    const view  = document.getElementById(viewId);
    const edit  = document.getElementById(editId);
    if (!btn || !view || !edit) return;

    function openEdit() {
        document.querySelectorAll('.edit-container').forEach(el => el.style.display = 'none');
        document.querySelectorAll('.info-row').forEach(el => el.classList.remove('hidden'));
        edit.style.display = 'block';
        view.classList.add('hidden');
    }
    function closeEdit() {
        edit.style.display = 'none';
        view.classList.remove('hidden');
    }

    btn.addEventListener('click', openEdit);
    if (close) close.addEventListener('click', closeEdit);
}

// ========================
//     TAB: COMMENTS
// ========================
async function fetchAndRenderComments() {
    setLoading();
    try {
        const res = await fetch(BASE_URL + 'user/comments');
        const result = await res.json();
        if (!result.data || result.data.length === 0) {
            setContent(`
                <h2 class="page-title">Ý KIẾN CỦA BẠN</h2>
                <div style="text-align:center;padding:40px;background:#f9f9f9;border-radius:8px;">
                    <i class="far fa-comments" style="font-size:40px;color:#ccc;margin-bottom:15px;display:block;"></i>
                    <p style="color:#666;">Bạn chưa bình luận vào bài viết nào.</p>
                </div>`);
            return;
        }

        let html = `
            <h3 style="border-left: 4px solid #17a2b8; padding-left: 10px; margin-bottom: 20px; color: #333;">
                Ý KIẾN CỦA BẠN
            </h3>
            <div class="news-list-vertical">`;
        result.data.forEach(item => {
            const statusText = item.status == 2
                ? '<span style="color:red">[Đã xóa]</span>'
                : (item.status == 0
                    ? '<span style="color:orange">[Chờ duyệt]</span>'
                    : '<span style="color:green">[Đã duyệt]</span>');
            const imgSrc = item.hinhanh ? SITE_NEWS_BASE + item.hinhanh : '';
            const date = formatDate(item.ngaybinh, true);
            html += `
            <div class="news-item-row">
                <a href="${BASE_URL}news/${item.news_id}" class="row-thumb">
                    <img src="${imgSrc}" onerror="this.src='${SITE_NEWS_BASE}default_news.jpg'" alt="">
                </a>
                <div class="row-body">
                    <a href="${BASE_URL}news/${item.news_id}" style="text-decoration:none;">
                        <h4>Bài viết: ${escapeHtml(item.news_title)}</h4>
                    </a>
                    <div class="my-comment-content">"${escapeHtml(item.noidung)}"</div>
                    <div class="row-meta">
                        <div><span>📅 ${date}</span> ${statusText}</div>
                        <button class="btn-delete-cmt" data-id="${item.id}">
                            <i class="fas fa-trash"></i> Xóa
                        </button>
                    </div>
                </div>
            </div>`;
        });
        html += `</div>`;
        setContent(html);

        document.querySelectorAll('.btn-delete-cmt').forEach(btn => {
            btn.addEventListener('click', async () => {
                if (!confirm('Bạn muốn xóa bình luận này?')) return;
                const result = await fetch(BASE_URL + 'user/deleteComment', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ comment_id: parseInt(btn.dataset.id) })
                }).then(r => r.json());
                alert(result.message);
                if (result.status === 'success') fetchAndRenderComments();
            });
        });
    } catch (e) {
        setContent('<p style="color:red;padding:20px;">Lỗi kết nối máy chủ.</p>');
    }
}

// ========================
//     TAB: BOOKMARKS
// ========================
async function fetchAndRenderBookmarks() {
    setLoading();
    try {
        const res = await fetch(BASE_URL + 'user/bookmarks');
        const result = await res.json();

        if (!result.data || result.data.length === 0) {
            setContent(`
                <h2 class="page-title">TIN ĐÃ LƯU CỦA BẠN</h2>
                <div style="text-align:center;padding:60px 20px;background:#f8f9fa;border-radius:12px;border:1px dashed #ced4da;">
                    <i class="far fa-bookmark" style="font-size:48px;color:#adb5bd;margin-bottom:20px;display:block;"></i>
                    <p style="color:#6c757d;font-size:16px;margin-bottom:20px;">Bạn chưa lưu tin tức nào vào bộ sưu tập.</p>
                    <a href="${BASE_URL}" style="display:inline-block;padding:10px 25px;background:#007bff;color:white;border-radius:30px;text-decoration:none;font-weight:500;">Khám phá tin mới</a>
                </div>`);
            return;
        }

        let html = `<h3 style="border-left: 4px solid #17a2b8; padding-left: 10px; margin-bottom: 20px; color: #333;">
                TIN ĐÃ LƯU CỦA BẠN
            </h3> <div class="news-grid-system">`;
        result.data.forEach(item => {
            const imgSrc = item.hinhanh ? SITE_NEWS_BASE + item.hinhanh : '';
            const date = formatDate(item.ngay_luu);
            html += `
            <div class="card-item">
                <a href="${BASE_URL}news/${item.id}" style="text-decoration:none;color:inherit;display:flex;flex-direction:column;height:100%;">
                    <div class="img-wrap">
                        <img src="${imgSrc}" onerror="this.src='${SITE_NEWS_BASE}default_news.jpg'">
                    </div>
                    <div class="card-body">
                        <h4>${escapeHtml(item.tieude)}</h4>
                        <p style="font-size:12px;color:#888;margin:0;">📅 ${date}</p>
                    </div>
                </a>
                <button class="btn-unbookmark" data-id="${item.id}"
                    style="display:block;width:100%;text-align:center;background:#fff5f5;color:#dc3545;padding:10px;font-size:13px;font-weight:600;border:none;border-top:1px solid #eee;cursor:pointer;">
                    <i class="fas fa-trash-alt"></i> Bỏ lưu
                </button>
            </div>`;
        });
        html += `</div>`;
        setContent(html);

        document.querySelectorAll('.btn-unbookmark').forEach(btn => {
            btn.addEventListener('click', async () => {
                if (!confirm('Bạn muốn bỏ lưu tin này?')) return;
                const result = await fetch(BASE_URL + 'user/deleteBookmark', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ news_id: parseInt(btn.dataset.id) })
                }).then(r => r.json());
                if (result.status === 'success') fetchAndRenderBookmarks();
            });
        });
    } catch (e) {
        setContent('<p style="color:red;padding:20px;">Lỗi kết nối máy chủ.</p>');
    }
}

// ========================
//     TAB: HISTORY
// ========================
async function fetchAndRenderHistory() {
    setLoading();
    try {
        const res = await fetch(BASE_URL + 'user/history');
        const result = await res.json();

        if (!result.data || result.data.length === 0) {
            setContent(`
                <h2 class="page-title">LỊCH SỬ XEM TIN</h2>
                <div style="text-align:center;padding:60px 20px;background:#f8f9fa;border-radius:12px;border:1px dashed #ced4da;">
                    <i class="fas fa-history" style="font-size:48px;color:#adb5bd;margin-bottom:20px;display:block;"></i>
                    <p style="color:#6c757d;font-size:16px;margin-bottom:20px;">Bạn chưa có lịch sử xem tin nào.</p>
                    <a href="${BASE_URL}" style="display:inline-block;padding:10px 25px;background:#007bff;color:white;border-radius:30px;text-decoration:none;font-weight:500;">Đọc báo ngay</a>
                </div>`);
            return;
        }

        let html = `
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
                <h3 style="border-left: 4px solid #17a2b8; padding-left: 10px; margin-bottom: 20px; color: #333;">
                LỊCH SỬ XEM TIN
            </h3>
                <button id="btn-clear-history" style="color:white;background:#dc3545;padding:6px 15px;border-radius:4px;border:none;cursor:pointer;font-size:13px;">
                    <i class="fas fa-trash-alt"></i> Xóa tất cả
                </button>
            </div>
            <div class="news-grid-system">`;

        result.data.forEach(item => {
            const imgSrc = item.hinhanh ? SITE_NEWS_BASE + item.hinhanh : '';
            const date = formatDate(item.ngaydang);
            html += `
            <div class="card-item">
                <a href="${BASE_URL}news/${item.id}" style="text-decoration:none;color:inherit;display:flex;flex-direction:column;height:100%;">
                    <div class="img-wrap">
                        <img src="${imgSrc}" onerror="this.src='${SITE_NEWS_BASE}default_news.jpg'">
                        <span style="position:absolute;top:10px;left:10px;background:rgba(0,0,0,0.6);color:#fff;font-size:11px;padding:3px 8px;border-radius:4px;">
                            ${escapeHtml(item.cat_name || 'Tin tức')}
                        </span>
                    </div>
                    <div class="card-body">
                        <h4>${escapeHtml(item.tieude)}</h4>
                        <p style="font-size:12px;color:#888;margin:0;">📅 ${date}</p>
                    </div>
                </a>
                <button class="btn-del-history" data-id="${item.id}"
                    style="display:block;width:100%;text-align:center;background:#fff5f5;color:#dc3545;padding:10px;font-size:13px;font-weight:600;border:none;border-top:1px solid #eee;cursor:pointer;">
                    <i class="fas fa-trash-alt"></i> Xóa
                </button>
            </div>`;
        });
        html += `</div>`;
        setContent(html);

        document.getElementById('btn-clear-history')?.addEventListener('click', async () => {
            if (!confirm('Bạn có chắc muốn xóa toàn bộ lịch sử xem tin?')) return;
            await fetch(BASE_URL + 'user/clearHistory', { method: 'POST' }).then(r => r.json());
            fetchAndRenderHistory();
        });

        document.querySelectorAll('.btn-del-history').forEach(btn => {
            btn.addEventListener('click', async () => {
                if (!confirm('Bạn muốn xóa bài viết này khỏi lịch sử?')) return;
                await fetch(BASE_URL + 'user/deleteHistoryItem', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ news_id: parseInt(btn.dataset.id) })
                });
                fetchAndRenderHistory();
            });
        });

    } catch (e) {
        setContent('<p style="color:red;padding:20px;">Lỗi kết nối máy chủ.</p>');
    }
}

// ========================
//       HELPERS
// ========================
function escapeHtml(unsafe) {
    return (unsafe || '').toString()
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

/**
 * Định dạng ngày theo kiểu dd/mm/yyyy HH:ii
 * Luôn có số 0 đứng trước (ví dụ: 08/09/2025 03:05)
 * @param {string} dateStr - chuỗi ngày từ phpMyAdmin (yyyy-mm-dd HH:ii:ss)
 * @param {boolean} showTime - có hiển thị giờ phút không
 */
function formatDate(dateStr, showTime = false) {
    if (!dateStr) return '';
    const d = new Date(dateStr);
    if (isNaN(d)) return dateStr;
    const dd  = String(d.getDate()).padStart(2, '0');
    const mm  = String(d.getMonth() + 1).padStart(2, '0');
    const yy  = d.getFullYear();
    if (!showTime) return `${dd}/${mm}/${yy}`;
    const hh  = String(d.getHours()).padStart(2, '0');
    const min = String(d.getMinutes()).padStart(2, '0');
    return `${dd}/${mm}/${yy} ${hh}:${min}`;
}
