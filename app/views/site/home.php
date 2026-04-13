<div id="top-ads-container"></div>

<div class="container main-wrapper" style="display: flex; gap: 30px; margin: 20px auto; max-width: 1200px; align-items: flex-start;">

    <div class="content-area" style="flex: 2;">
        <h2 style="border-left: 5px solid #00b686; padding-left: 15px; margin-bottom: 30px; font-weight: bold; font-size: 24px;">TIN MỚI NHẤT</h2>
        <div id="loading" style="text-align:center; padding:50px;">Đang tải tin tức...</div>
        <div class="news-list" id="home-news-list"></div>
        <div id="pagination-container" class="pagination" style="display: flex; justify-content: center; gap: 8px; margin-top: 40px;"></div>
    </div>

    <aside style="flex: 0 0 300px; max-width: 300px; position: sticky; top: 10px;">
        <div style="border: 1px solid #eee; border-radius: 8px; overflow: hidden; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
            <h3 style="background: #333; color: #fff; padding: 12px; margin: 0; font-size: 16px; text-transform: uppercase;">🔥 Tin xem nhiều</h3>
            <div style="padding: 15px; background: #fff;" id="top-views-container"></div>
        </div>

        <div id="sidebar-ads-container" class="widget-ads"></div>
    </aside>
</div>

<div id="footer-ads-container"></div>

<script>const BASE_URL = '<?= URLROOT ?>';</script>
<script src="<?= URLROOT ?>assets/js/site/home.js"></script>
