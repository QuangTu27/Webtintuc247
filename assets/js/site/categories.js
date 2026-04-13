async function fetchCategoryData() {
    try {
        const res = await fetch(`${BASE_URL}categories/${catId}/data?page=${page}`);
        const result = await res.json();

        if (result.status === 'success') {
            const data = result.data;
            const parent = data.parent_info;
            const subs = data.sub_categories;

            const parentLink = document.getElementById('parent-link');
            if (parentLink && parent) {
                parentLink.href = `${BASE_URL}categories/${parent.id}`;
                parentLink.innerText = parent.name;
                parentLink.title = parent.name;
            }

            let subHtml = '';
            if (subs) {
                subs.forEach(s => {
                    let active = (s.id == catId) ? 'active' : '';
                    subHtml += `<li><a href="${BASE_URL}categories/${s.id}" class="${active}">${s.name}</a></li>`;
                });
                const subNav = document.getElementById('sub-categories-nav');
                if(subNav) subNav.innerHTML = subHtml;
            }

            const list = document.getElementById('category-news-list');
            const pNav = document.getElementById('pagination-container');

            if (data.news && data.news.length > 0) {
                let newsHtml = '';
                data.news.forEach(news => {
                    let desc = news.tomtat ? news.tomtat : 'Nhấn vào để đọc tiếp bài viết...';
                    newsHtml += `
                        <div class="cat-news-item">
                            <a href="${BASE_URL}news/${news.id}" class="cat-thumb">
                                <img src="${BASE_URL}assets/images/news/${news.hinhanh}" onerror="this.src='${BASE_URL}assets/images/default_news.jpg'">
                            </a>
                            <div class="cat-info">
                                <h3>
                                    <a href="${BASE_URL}news/${news.id}">
                                        ${news.tieude}
                                    </a>
                                </h3>
                                <p class="cat-sapo">
                                    ${desc.substring(0, 150)}...
                                </p>
                            </div>
                        </div>`;
                });
                if(list) list.innerHTML = newsHtml;

                if (data.pagination && data.pagination.total_pages > 1) {
                    let pgHtml = '';
                    const p = data.pagination;
                    if (p.page > 1) pgHtml += `<a href="${BASE_URL}categories/${catId}?page=${p.page - 1}">«</a>`;
                    for (let i = 1; i <= p.total_pages; i++) {
                        let act = (i == p.page) ? 'class="active"' : '';
                        pgHtml += `<a href="${BASE_URL}categories/${catId}?page=${i}" ${act}>${i}</a>`;
                    }
                    if (p.page < p.total_pages) pgHtml += `<a href="${BASE_URL}categories/${catId}?page=${p.page + 1}">»</a>`;
                    if(pNav) pNav.innerHTML = pgHtml;
                }
            } else {
                if(list) list.innerHTML = '<div style="padding: 30px; background: #f9f9f9; text-align: center; border-radius: 8px;"><p>📭 Chưa có bài viết nào.</p></div>';
            }

            let topHtml = '';
            if (data.top_views) {
                data.top_views.forEach(t => {
                    topHtml += `
                        <div style="display: flex; gap: 10px; margin-bottom: 15px; align-items: flex-start; border-bottom: 1px dashed #eee; padding-bottom: 10px;">
                            <img src="${BASE_URL}assets/images/news/${t.hinhanh}" style="width: 70px; height: 50px; object-fit: cover; flex-shrink: 0;" onerror="this.src='${BASE_URL}assets/images/default_news.jpg'">
                            <a href="${BASE_URL}news/${t.id}" style="font-size: 13px; text-decoration: none; color: #333; font-weight: 500; line-height: 1.4;">
                                ${t.tieude.substring(0, 50)}...
                            </a>
                        </div>`;
                });
                const topViews = document.getElementById('top-views-container');
                if(topViews) topViews.innerHTML = topHtml;
            }

            const loading = document.getElementById('loading');
            if(loading) loading.style.display = 'none';
        } else {
            const loading = document.getElementById('loading');
            if(loading) loading.innerHTML = result.message;
        }
    } catch (e) {
        const loading = document.getElementById('loading');
        if(loading) loading.innerHTML = "Lỗi kết nối!";
    }
}

document.addEventListener('DOMContentLoaded', () => {
    if (!catId) {
        const container = document.querySelector('.container');
        if(container) container.innerHTML = "<div style='padding:50px; text-align:center;'><h3>❌ Danh mục không tồn tại!</h3></div>";
    } else {
        fetchCategoryData();
    }
});
