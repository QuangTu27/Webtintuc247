<div class="loading-news" id="loader" style="text-align:center; padding: 100px; font-size:18px; color:#888;">Đang tải bài viết...</div>

<div class="container" id="news-container" style="max-width: 900px; margin: 30px auto; padding: 0 15px; display:none;">
    <article class="news-detail" style="background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">

        <nav id="news-nav" style="font-size: 14px; color: #888; margin-bottom: 20px;"></nav>

        <h1 id="news-title" style="font-size: 36px; line-height: 1.3; color: #222; margin-bottom: 20px; font-weight: 800;"></h1>

        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 25px;">
            <div style="color: #999; font-size: 13px;" id="news-meta"></div>

            <div style="display: flex; gap: 10px;">
                <button id="btn-like" onclick="toggleLike()" style="cursor:pointer; border:1px solid #0a9e54; padding: 6px 15px; border-radius: 4px; background: #fff; color: #0a9e54; font-size: 13px; font-weight: bold;">
                    <i class="far fa-thumbs-up"></i> Thích (<span id="like-count">0</span>)
                </button>

                <a id="btn-save" href="javascript:void(0)" onclick="toggleSave()" style="background: #f8f9fa; border: 1px solid #ddd; padding: 6px 15px; border-radius: 4px; text-decoration: none; color: #666; font-size: 13px; font-weight: bold;">
                    <i class="far fa-bookmark"></i> Lưu tin
                </a>
            </div>
        </div>

        <div class="sapo" id="news-sapo" style="font-size: 20px; font-weight: 700; line-height: 1.6; color: #444; margin-bottom: 30px; border-left: 5px solid #28a745; padding-left: 20px;"></div>

        <div style="text-align: center; margin-bottom: 30px;">
            <img id="news-thumb" src="" style="max-width: 100%; height: auto; border-radius: 5px;">
        </div>

        <div class="main-content" id="news-content"></div>

        <div class="comment-section" style="margin-top: 50px; border-top: 2px solid #333; padding-top: 30px;">
            <h3 style="margin-bottom: 20px;"><i class="far fa-comments"></i> Bình luận</h3>
            <div id="comment-box-area" style="margin-bottom: 30px;"></div>
            <div id="comment-list"></div>
        </div>

        <button id="btn-share" style="cursor:pointer; border:1px solid #007bff; padding: 6px 15px; border-radius: 4px; background: #fff; color: #007bff; font-size: 13px; font-weight: bold; display: flex; align-items: center; gap: 5px;">
            <i class="fas fa-share-alt"></i> Chia sẻ
        </button>

        <span id="share-success" style="display: none; color: #28a745; font-size: 12px; font-weight: bold; margin-left: 10px;">
            <i class="fas fa-check"></i> Đã sao chép link!
        </span>
        <div style="margin-top: 50px; padding-top: 20px; border-top: 1px solid #eee;">
            <p style="font-weight: bold; color: #000;">Nguồn: TINTUC24H</p>
            <a href="javascript:history.back()" style="color: #007bff; text-decoration: none;">← Quay lại trang trước</a>
        </div>
    </article>
</div>

<script>
    const BASE_URL = '<?= URLROOT ?>';
    const nId = <?= $newsId ?? 0 ?>;
</script>
<script src="<?= URLROOT ?>assets/js/site/news_detail.js"></script>
