<link rel="stylesheet" href="<?= URLROOT ?>assets/css/site/search.css">

<div class="search-container">
    <h2 class="search-title">
        <i class="fas fa-search"></i> Kết quả tìm kiếm cho: 
        <strong class="highlight-keyword">"<?= htmlspecialchars($data['keyword'] ?? '') ?>"</strong>
    </h2>
    <p class="search-meta">Tìm thấy <?= $data['total'] ?? 0 ?> bài viết phù hợp.</p>

    <div class="search-result-list">
        <?php if (!empty($data['news'])): ?>
            <?php foreach ($data['news'] as $news): ?>
                <div class="search-item">
                    <a href="<?= URLROOT ?>news/<?= $news->id ?>" class="item-thumb-link">
                        <img src="<?= URLROOT ?>assets/images/news/<?= htmlspecialchars($news->hinhanh) ?>" 
                             onerror="this.onerror=null; this.src='<?= URLROOT ?>assets/images/default_news.jpg'" 
                             class="item-thumb-img" alt="<?= htmlspecialchars($news->tieude) ?>">
                    </a>
                    
                    <div class="item-content">
                        <a href="<?= URLROOT ?>news/<?= $news->id ?>" class="item-title-link">
                            <h3 class="item-title"><?= htmlspecialchars($news->tieude) ?></h3>
                        </a>
                        <p class="item-summary">
                            <?= htmlspecialchars($news->tomtat ?? '') ?>
                        </p>
                        <div class="item-details">
                            <span class="item-category"><?= htmlspecialchars($news->category_name ?? 'Tin tức') ?></span>
                            <span><i class="far fa-clock"></i> <?= date('d/m/Y H:i', strtotime($news->ngaydang)) ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="search-empty">
                <i class="fas fa-folder-open empty-icon"></i>
                <p>Không tìm thấy bài viết nào phù hợp.</p>
                <a href="<?= URLROOT ?>" class="back-home-link">← Quay lại Trang chủ</a>
            </div>
        <?php endif; ?>
    </div>

    <?php if (isset($data['total_pages']) && $data['total_pages'] > 1): ?>
        <div class="search-pagination">
            <?php if ($data['page'] > 1): ?>
                <a href="<?= URLROOT ?>search?keyword=<?= urlencode($data['keyword']) ?>&page=<?= $data['page'] - 1 ?>" class="page-link">«</a>
            <?php endif; ?>
            
            <?php for ($i = 1; $i <= $data['total_pages']; $i++): ?>
                <?php $activeClass = ($i == $data['page']) ? 'active' : ''; ?>
                <a href="<?= URLROOT ?>search?keyword=<?= urlencode($data['keyword']) ?>&page=<?= $i ?>" class="page-link <?= $activeClass ?>"><?= $i ?></a>
            <?php endfor; ?>
            
            <?php if ($data['page'] < $data['total_pages']): ?>
                <a href="<?= URLROOT ?>search?keyword=<?= urlencode($data['keyword']) ?>&page=<?= $data['page'] + 1 ?>" class="page-link">»</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>