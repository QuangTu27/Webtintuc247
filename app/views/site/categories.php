<link rel="stylesheet" href="<?= URLROOT ?>assets/css/site/category.css">

<div class="container" style="margin-top: 20px;">
    <div class="cat-header-wrapper">
        <h1 class="cat-title-large">
            <a href="#" id="parent-link">Đang tải...</a>
        </h1>
        <ul class="cat-sub-nav" id="sub-categories-nav"></ul>
    </div>
</div>

<div class="container main-wrapper" style="display: flex; gap: 30px; margin-top: 20px; max-width: 1200px; align-items: flex-start;">

    <div class="content-area" style="flex: 2;">
        <div id="loading" style="text-align:center; padding: 50px;">Đang tải tin tức...</div>
        <div class="category-news-list" id="category-news-list"></div>
        <div class="pagination" id="pagination-container"></div>
    </div>

    <aside style="flex: 0 0 300px; max-width: 300px; position: sticky; top: 20px;">
        <div style="border: 1px solid #eee; border-radius: 4px; overflow: hidden;">
            <h3 style="background: #f7f7f7; color: #333; padding: 10px 15px; margin: 0; font-size: 14px; text-transform: uppercase; border-bottom: 1px solid #eee; font-weight: bold;">
                Đọc nhiều
            </h3>
            <div style="padding: 15px; background: #fff;" id="top-views-container"></div>
        </div>
    </aside>

</div>

<script>
    const BASE_URL = '<?= URLROOT ?>';
    const catId = <?= $catId ?? 0 ?>;
    const page = <?= $page ?? 1 ?>;
</script>
<script src="<?= URLROOT ?>assets/js/site/categories.js"></script>
