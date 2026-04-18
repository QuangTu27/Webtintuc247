<link rel="stylesheet" href="<?= URLROOT ?>assets/css/site/search.css">

<div class="search-container">
    <h2 class="search-title">
        <i class="fas fa-search"></i> Kết quả tìm kiếm cho:
        <strong class="highlight-keyword" id="search-keyword-display"></strong>
    </h2>
    <p class="search-meta" id="search-meta">Đang tải...</p>

    <div class="search-result-list" id="search-result-list">
        <div id="search-loading" style="text-align:center; padding: 40px; color: #888;">
            <i class="fas fa-spinner fa-spin"></i> Đang tìm kiếm...
        </div>
    </div>

    <div id="search-pagination" class="search-pagination"></div>
</div>

<script>
    const BASE_URL = '<?= URLROOT ?>';
</script>
<script src="<?= URLROOT ?>assets/js/site/search.js"></script>