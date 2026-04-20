const urlParams = new URLSearchParams(window.location.search);
let currentPage = urlParams.get('page') ? parseInt(urlParams.get('page')) : 1;
let globalAds = {};

async function fetchHomeData() {
    try {
        const res = await fetch(`${BASE_URL}api/site/home?page=${currentPage}`);
        const result = await res.json();

        if (result.status === 'success') {
            const data = result.data;
            globalAds = data.ads || {};

            renderAds('top-ads-container', 'top_home', 'home-top-ads container');
            renderAds('sidebar-ads-container', 'sidebar_right', '');
            renderAds('footer-ads-container', 'footer_home', 'home-footer-ads container');

            renderNewsData(data.news);
            renderTopViews(data.top_views);
            renderPagination(data.pagination);

            initSliders();
            const loading = document.getElementById('loading');
            if(loading) loading.style.display = 'none';
        }
    } catch (e) {
        const loading = document.getElementById('loading');
        if(loading) loading.innerHTML = 'Có lỗi xảy ra khi tải dữ liệu.';
    }
}

function generateAdsHtml(position) {
    if (!globalAds[position] || globalAds[position].length === 0) return '';
    let html = '<div class="ads-slider" data-speed="5000">';
    globalAds[position].forEach((ad, index) => {
        let active = index === 0 ? 'active' : '';
        let media = ad.media_type === 'video'
            ? `<video autoplay muted loop playsinline class="ads-video" style="width:100%"><source src="${BASE_URL}assets/images/ads/${ad.media_file}" type="video/mp4"></video>`
            : `<img src="${BASE_URL}assets/images/ads/${ad.media_file}" alt="${ad.title}" style="width:100%">`;
        html += `<div class="ads-item ${active}"><a href="${ad.link}" target="_blank">${media}</a></div>`;
    });
    html += '</div>';
    return html;
}

function renderAds(containerId, position, wrapperClass) {
    let adsHtml = generateAdsHtml(position);
    if (adsHtml) {
        const container = document.getElementById(containerId);
        if(container) container.innerHTML = wrapperClass ? `<div class="${wrapperClass}">${adsHtml}</div>` : adsHtml;
    }
}

function renderNewsData(newsArr) {
    const list = document.getElementById('home-news-list');
    if(!list) return;
    if (!newsArr || newsArr.length === 0) {
        list.innerHTML = '<p>Hiện tại chưa có bài viết nào.</p>';
        return;
    }

    let html = '';
    newsArr.forEach((news, index) => {
        let date = new Date(news.ngaydang);
        let dateStr = `${date.getDate()}/${date.getMonth()+1}/${date.getFullYear()}`;
        let desc = news.tomtat || 'Nhấn vào để đọc tiếp bài viết...';

        html += `
            <div class="news-item" style="display: flex; gap: 20px; margin-bottom: 25px; padding-bottom: 20px; border-bottom: 1px solid #eee;">
                <div class="news-thumb-frame" style="flex: 0 0 240px; height: 160px; overflow: hidden; border-radius: 8px;">
                    <a href="${BASE_URL}news/${news.id}">
                        <img src="${BASE_URL}assets/images/news/${news.hinhanh}" onerror="this.src='${BASE_URL}assets/images/default_news.jpg'" style="width: 100%; height: 100%; object-fit: cover;">
                    </a>
                </div>
                <div class="news-info" style="flex: 1;">
                    <h3 style="margin: 0 0 10px 0;">
                        <a href="${BASE_URL}news/${news.id}" style="text-decoration: none; color: #222; font-weight: bold; font-size: 19px; line-height: 1.3; display: block;">
                            ${news.tieude}
                        </a>
                    </h3>
                    <p style="color: #666; font-size: 14.5px; line-height: 1.5; margin-bottom: 10px;">
                        ${desc.substring(0, 160)}...
                    </p>
                    <div style="font-size: 12px; color: #999;">
                        <span>📅 ${dateStr}</span>
                        <span style="margin-left: 15px;">👁️ ${parseInt(news.view_count).toLocaleString()} lượt xem</span>
                    </div>
                </div>
            </div>`;

        if (index === 4 && globalAds['inline_home']) {
            let inlineHtml = generateAdsHtml('inline_home');
            if (inlineHtml) {
                html += `<div class="ads-inline" style="margin: 20px 0;">${inlineHtml}</div>`;
            }
        }
    });
    list.innerHTML = html;
}

function renderTopViews(tops) {
    let html = '';
    tops.forEach(t => {
        html += `
            <div style="display: flex; gap: 10px; margin-bottom: 15px; align-items: flex-start; border-bottom: 1px dashed #eee; padding-bottom: 10px;">
                <img src="${BASE_URL}assets/images/news/${t.hinhanh}" style="width: 70px; height: 50px; object-fit: cover; border-radius: 4px; flex-shrink: 0;" onerror="this.src='${BASE_URL}assets/images/default_news.jpg'">
                <a href="${BASE_URL}news/${t.id}" style="font-size: 13px; text-decoration: none; color: #333; font-weight: 500; line-height: 1.4;">
                    ${t.tieude.substring(0, 60)}...
                </a>
            </div>`;
    });
    const cont = document.getElementById('top-views-container');
    if(cont) cont.innerHTML = html;
}

function renderPagination(pg) {
    if (pg.total_pages <= 1) return;
    let html = '';
    if (pg.page > 1) html += `<a href="${BASE_URL}?page=${pg.page - 1}" style="padding: 8px 16px; border: 1px solid #ddd; text-decoration: none; color: #333; border-radius: 4px;">« Trước</a>`;

    for (let i = 1; i <= pg.total_pages; i++) {
        if (i === pg.page) {
            html += `<span style="padding: 8px 16px; background: #00b686; color: #fff; border-radius: 4px; font-weight: bold;">${i}</span>`;
        } else {
            html += `<a href="${BASE_URL}?page=${i}" style="padding: 8px 16px; border: 1px solid #ddd; text-decoration: none; color: #333; border-radius: 4px;">${i}</a>`;
        }
    }

    if (pg.page < pg.total_pages) html += `<a href="${BASE_URL}?page=${pg.page + 1}" style="padding: 8px 16px; border: 1px solid #ddd; text-decoration: none; color: #333; border-radius: 4px;">Sau »</a>`;

    const cont = document.getElementById('pagination-container');
    if(cont) cont.innerHTML = html;
}

function initSliders() {
    const sliders = document.querySelectorAll('.ads-slider');
    sliders.forEach(slider => {
        const items = slider.querySelectorAll('.ads-item');
        if (items.length <= 1) return;
        let currentIndex = 0;
        const speed = parseInt(slider.getAttribute('data-speed')) || 5000;
        setInterval(() => {
            items[currentIndex].classList.remove('active');
            currentIndex = (currentIndex + 1) % items.length;
            items[currentIndex].classList.add('active');
            const video = items[currentIndex].querySelector('video');
            if (video) { video.currentTime = 0; video.play(); }
        }, speed);
    });
}

window.addEventListener('load', fetchHomeData);
