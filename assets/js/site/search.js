const urlParams = new URLSearchParams(window.location.search);
const keyword   = urlParams.get('keyword') || '';
let   page      = parseInt(urlParams.get('page')) || 1;

document.addEventListener('DOMContentLoaded', () => {
    const keywordDisplay = document.getElementById('search-keyword-display');
    if (keywordDisplay) keywordDisplay.textContent = `"${keyword}"`;
    fetchSearchData();
});

async function fetchSearchData() {
    const loading    = document.getElementById('search-loading');
    const resultList = document.getElementById('search-result-list');
    const metaEl     = document.getElementById('search-meta');
    const paginEl    = document.getElementById('search-pagination');

    if (!keyword) {
        if (loading) loading.style.display = 'none';
        if (metaEl)  metaEl.textContent = 'Vui lòng nhập từ khóa để tìm kiếm.';
        return;
    }

    try {
        const res    = await fetch(`${BASE_URL}search/data?keyword=${encodeURIComponent(keyword)}&page=${page}`);
        const result = await res.json();

        if (loading) loading.style.display = 'none';

        if (result.status !== 'success') {
            if (resultList) resultList.innerHTML = '<p style="color:red;">Lỗi khi tải kết quả.</p>';
            return;
        }

        const data = result.data;

        if (metaEl) metaEl.textContent = `Tìm thấy ${data.total} bài viết phù hợp.`;

        if (data.news && data.news.length > 0) {
            let html = '';
            data.news.forEach(news => {
                const sapo = news.tomtat ? news.tomtat.substring(0, 150) + '...' : '';
                html += `
                <div class="search-item">
                    <a href="${BASE_URL}news/${news.id}" class="item-thumb-link">
                        <img src="${BASE_URL}assets/images/news/${news.hinhanh}"
                             onerror="this.src='${BASE_URL}assets/images/default_news.jpg'"
                             class="item-thumb-img" alt="${news.tieude}">
                    </a>
                    <div class="item-content">
                        <a href="${BASE_URL}news/${news.id}" class="item-title-link">
                            <h3 class="item-title">${news.tieude}</h3>
                        </a>
                        <p class="item-summary">${sapo}</p>
                        <div class="item-details">
                            <span class="item-category">${news.cat_name || 'Tin tức'}</span>
                            <span><i class="far fa-clock"></i> ${news.ngaydang}</span>
                        </div>
                    </div>
                </div>`;
            });
            if (resultList) resultList.innerHTML = html;
        } else {
            if (resultList) resultList.innerHTML = `
                <div class="search-empty">
                    <i class="fas fa-folder-open empty-icon"></i>
                    <p>Không tìm thấy bài viết nào phù hợp.</p>
                    <a href="${BASE_URL}" class="back-home-link">← Quay lại Trang chủ</a>
                </div>`;
        }

        // Pagination
        const p = data.pagination;
        if (paginEl && p.total_pages > 1) {
            let pgHtml = '';
            if (p.page > 1) pgHtml += `<a href="${BASE_URL}search?keyword=${encodeURIComponent(keyword)}&page=${p.page - 1}" class="page-link">«</a>`;
            for (let i = 1; i <= p.total_pages; i++) {
                const active = i === p.page ? 'active' : '';
                pgHtml += `<a href="${BASE_URL}search?keyword=${encodeURIComponent(keyword)}&page=${i}" class="page-link ${active}">${i}</a>`;
            }
            if (p.page < p.total_pages) pgHtml += `<a href="${BASE_URL}search?keyword=${encodeURIComponent(keyword)}&page=${p.page + 1}" class="page-link">»</a>`;
            paginEl.innerHTML = pgHtml;
        }

    } catch (e) {
        if (loading) loading.innerHTML = 'Lỗi kết nối!';
    }
}
