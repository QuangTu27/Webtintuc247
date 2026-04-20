let sessionState = { logged_in: false, user_id: 0 };
let currentData = null;

document.addEventListener('DOMContentLoaded', () => {
    if (!nId) window.location.href = BASE_URL;
    else fetchNewsDetail();

    const btnShare = document.getElementById('btn-share');
    if (btnShare) {
        btnShare.addEventListener('click', function() {
            navigator.clipboard.writeText(window.location.href).then(() => {
                const successMsg = document.getElementById('share-success');
                if (successMsg) {
                    successMsg.style.display = 'inline';
                    setTimeout(() => { successMsg.style.display = 'none'; }, 2000);
                }
            });
        });
    }
});

async function fetchNewsDetail() {
    try {
        const res = await fetch(`${BASE_URL}api/site/news/${nId}`);
        const result = await res.json();

        if (result.status === 'success') {
            currentData = result.data;
            sessionState = currentData.session;
            renderNewsBox();
        } else {
            const loader = document.getElementById('loader');
            if(loader) loader.innerHTML = `<h2 style='color:#d9534f;'>⚠️ ${result.message}</h2>`;
        }
    } catch(e) {
        const loader = document.getElementById('loader');
        if(loader) loader.innerText = "Lỗi mạng khi tải chi tiết bài viết!";
    }
}

function renderNewsBox() {
    const { news, likes, is_liked, is_saved, comments } = currentData;

    let navHtml = '';
    if (news.parent_name) navHtml += `<a href="${BASE_URL}categories/${news.parent_id}" style="color:#333; text-decoration:none;">${news.parent_name}</a> <span style="margin: 0 5px;">></span> `;
    navHtml += `<a href="${BASE_URL}categories/${news.cat_id}" style="color:#333; text-decoration:none; font-weight:600;">${news.cat_name || 'Tin tức'}</a>`;
    const newsNav = document.getElementById('news-nav');
    if(newsNav) newsNav.innerHTML = navHtml;

    const navTitle = document.getElementById('news-title');
    if(navTitle) navTitle.innerText = news.tieude;
    
    const newsMeta = document.getElementById('news-meta');
    if(newsMeta) newsMeta.innerHTML = `<span style="margin-right:15px;">📅 ${news.ngaydang}</span><span style="margin-right:15px;"><i class="fas fa-user-edit"></i> ${news.author_name || 'Ẩn danh'}</span><span>👁️ ${parseInt(news.view_count).toLocaleString('vi-VN')} lượt xem</span>`;

    const btnLike = document.getElementById('btn-like');
    const likeCount = document.getElementById('like-count');
    if(likeCount) likeCount.innerText = likes;
    
    if (btnLike) {
        if (is_liked) {
            btnLike.style.background = '#0a9e54';
            btnLike.style.color = '#fff';
            btnLike.innerHTML = `<i class="fas fa-thumbs-up"></i> Thích (<span id="like-count">${likes}</span>)`;
        } else {
            btnLike.style.background = '#fff';
            btnLike.style.color = '#666';
            btnLike.innerHTML = `<i class="far fa-thumbs-up"></i> Thích (<span id="like-count">${likes}</span>)`;
        }
    }

    const btnSave = document.getElementById('btn-save');
    if (btnSave) {
        if (is_saved) {
            btnSave.style.background = '#e9ecef';
            btnSave.style.color = '#000';
            btnSave.innerHTML = `<i class="fas fa-check"></i> Đã lưu`;
        } else {
            btnSave.style.background = '#ffc107';
            btnSave.style.color = '#000';
            btnSave.innerHTML = `<i class="far fa-bookmark"></i> Lưu tin`;
        }
    }

    const sapo = document.getElementById('news-sapo');
    if (sapo) {
        if (news.tomtat) {
            sapo.innerText = news.tomtat;
        } else {
            sapo.style.display = 'none';
        }
    }

    const thumb = document.getElementById('news-thumb');
    if (thumb) thumb.src = BASE_URL + 'assets/images/news/' + news.hinhanh;
    
    const content = document.getElementById('news-content');
    if (content) content.innerHTML = news.noidung;

    const cbArea = document.getElementById('comment-box-area');
    if (cbArea) {
        if (sessionState.logged_in) {
            cbArea.innerHTML = `
                <textarea id="comment-content" placeholder="Chia sẻ ý kiến của bạn..." style="width: 100%; height: 80px; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-family: inherit; margin-bottom: 10px;"></textarea>
                <button onclick="postComment()" style="background: #0a9e54; color: #fff; border: none; padding: 10px 25px; border-radius: 4px; cursor: pointer; font-weight: bold;">Gửi bình luận</button>
            `;
        } else {
            cbArea.innerHTML = `<p style="background: #fff3cd; padding: 15px; border-radius: 4px; border: 1px solid #ffeeba;">Vui lòng <a href="javascript:void(0)" onclick="openAuthModal('login')" style="font-weight: bold; color: #856404;">Đăng nhập</a> để tham gia bình luận.</p>`;
        }
    }

    renderCommentsList(comments);

    const loader = document.getElementById('loader');
    if (loader) loader.style.display = 'none';
    
    const newsCont = document.getElementById('news-container');
    if (newsCont) newsCont.style.display = 'block';
}

function renderCommentsList(comments) {
    let parents = comments.filter(c => !c.parent_id);
    let children = comments.filter(c => c.parent_id);
    let html = '';

    parents.forEach(p => {
        html += generateCommentHTML(p);
        let rep = children.filter(c => c.parent_id == p.id);
        if (rep.length > 0) {
            html += `<div style="margin-left:40px; margin-top:15px; border-left:2px solid #eee; padding-left:15px;">`;
            rep.forEach(c => { html += generateCommentHTML(c, true); });
            html += `</div>`;
        }
        if (sessionState.logged_in) {
            html += `
            <div id="reply-form-${p.id}" style="display:none; margin-top:15px; margin-left:40px;">
                <textarea id="input-reply-${p.id}" placeholder="Viết phản hồi..." style="width:100%; height:60px; padding:10px; border:1px solid #ddd; border-radius:4px; margin-bottom:5px;"></textarea>
                <button onclick="postComment(${p.id})" style="background:#007bff; color:#fff; border:none; padding:5px 15px; border-radius:4px; cursor:pointer;">Gửi trả lời</button>
                <button onclick="document.getElementById('reply-form-${p.id}').style.display='none'" style="background:#6c757d; color:#fff; border:none; padding:5px 15px; border-radius:4px; cursor:pointer;">Huỷ</button>
            </div>`;
        }
        html += `</div>`;
    });
    const cList = document.getElementById('comment-list');
    if (cList) cList.innerHTML = html;
}

function generateCommentHTML(c, isChild = false) {
    let sttHtml = c.status == 2 ? `<em style="color:#999;">Bình luận này đã bị xoá.</em>` : c.noidung;
    let actionsHtml = '';
    if (c.status == 1 && sessionState.logged_in) {
        if (!isChild) actionsHtml += `<a href="javascript:void(0)" onclick="document.getElementById('reply-form-${c.id}').style.display='block'" style="color:#007bff; text-decoration:none; margin-right:15px;"><i class="fas fa-reply"></i> Trả lời</a>`;
        if (sessionState.user_id == c.user_id) {
            actionsHtml += `
                <a href="javascript:void(0)" onclick="openEdit(${c.id})" style="color:#888; text-decoration:none; margin-right:15px;"><i class="fas fa-edit"></i> Sửa</a>
                <a href="javascript:void(0)" onclick="execDelete(${c.id})" style="color:#dc3545; text-decoration:none;"><i class="fas fa-trash"></i>Xoá</a>
            `;
        }
    }

    let boxStyle = isChild ? `margin-bottom:15px; padding-bottom:10px; border-bottom:1px dashed #eee;` : `margin-bottom:20px; padding-bottom:15px; border-bottom:1px solid #f1f1f1;`;

    return `
    <div class="comment-item" style="${boxStyle}">
        <div class="comment-content">
            <strong style="color:#28a745;">${c.ten_nguoi_binh}</strong>
            <small style="color:#bbb; margin-left:10px;">${c.ngaybinh}</small>
            <div id="text-${c.id}" style="margin:8px 0; color:#333;">${sttHtml}</div>
            <div style="font-size:13px; margin-top:5px;">${actionsHtml}</div>
            <div id="edit-form-${c.id}" style="display:none; margin-top:10px;">
                <textarea id="input-edit-${c.id}" style="width:100%; height:60px; padding:10px; border:1px solid #ddd; border-radius:4px; margin-bottom:5px;">${c.noidung}</textarea>
                <button onclick="execEdit(${c.id})" style="background:#28a745; color:#fff; border:none; padding:5px 15px; border-radius:4px; cursor:pointer;">Lưu</button>
                <button onclick="closeEdit(${c.id})" style="background:#6c757d; color:#fff; border:none; padding:5px 15px; border-radius:4px; cursor:pointer;">Huỷ</button>
            </div>
        </div>
    `;
}

async function toggleLike() {
    if (!sessionState.logged_in) { alert("Bạn cần đăng nhập!"); return; }
    const res = await fetch(`${BASE_URL}api/site/news/like`, { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({action:'toggle_like', news_id: nId}) });
    const data = await res.json();
    if (data.status === 'success') { fetchNewsDetail(); }
}

async function toggleSave() {
    if (!sessionState.logged_in) { alert("Bạn cần đăng nhập!"); return; }
    const res = await fetch(`${BASE_URL}api/site/news/save`, { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({action:'toggle_save', news_id: nId}) });
    const data = await res.json();
    if (data.status === 'success') { fetchNewsDetail(); }
}

async function postComment(parentId = null) {
    let elId = parentId ? `input-reply-${parentId}` : `comment-content`;
    let contentEl = document.getElementById(elId);
    if (!contentEl) return;
    let nd = contentEl.value;
    if (nd.trim() === '') { alert("Vui lòng nhập nội dung!"); return; }
    const res = await fetch(`${BASE_URL}api/site/news/comment`, { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({action:'post_comment', news_id: nId, parent_id: parentId, noidung: nd}) });
    const data = await res.json();
    if (data.status === 'success') { 
        fetchNewsDetail(); 
    }
    else { alert("Lỗi: " + data.message); }
}

function openEdit(id) { 
    document.getElementById(`text-${id}`).style.display = 'none'; 
    document.getElementById(`edit-form-${id}`).style.display = 'block'; 
}

function closeEdit(id) { 
    document.getElementById(`text-${id}`).style.display = 'block'; 
    document.getElementById(`edit-form-${id}`).style.display = 'none'; 
}

async function execEdit(id) {
    let nd = document.getElementById(`input-edit-${id}`).value;
    if (nd.trim() === '') { alert("Vui lòng nhập nội dung!"); return; }
    const res = await fetch(`${BASE_URL}api/site/news/comment`, { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({action:'edit_comment', comment_id: id, noidung: nd}) });
    const data = await res.json();
    if (data.status === 'success') { 
        const textDiv = document.getElementById(`text-${id}`);
        if (textDiv) textDiv.innerText = nd;
        closeEdit(id);
    }
    else alert("Lỗi: " + data.message);
}

async function execDelete(id) {
    if (!confirm("Bạn chắc muốn thao tác này?")) return;
    const res = await fetch(`${BASE_URL}api/site/news/comment`, { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({action:'delete_comment', comment_id: id}) });
    const data = await res.json();
    if (data.status === 'success') { fetchNewsDetail(); }
    else alert("Lỗi: " + data.message);
}
