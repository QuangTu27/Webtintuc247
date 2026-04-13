<div class="admin-container">
    <div class="admin-header-inline">
        <h2 class="admin-title">CHỈNH SỬA BÀI VIẾT</h2>
    </div>

    <div id="status-msg" style="display:none; padding:10px; margin-bottom:15px; border-radius:4px;" class="alert alert-warning"></div>
    <div id="loading" style="text-align:center; padding: 50px;">Đang tải dữ liệu bài viết...</div>

    <form id="editNewsForm" enctype="multipart/form-data" class="admin-form" style="display:none;">
        <input type="hidden" name="id" id="news_id" value="">

        <div class="form-group">
            <label>Tiêu đề bài viết</label>
            <input type="text" name="tieude" id="tieude" required>
        </div>

        <div class="form-group">
            <label>Danh mục</label>
            <select name="danhmuc" id="danhmuc" class="form-control" required>
                <option value="">-- Chọn danh mục --</option>
            </select>
        </div>

        <div class="form-group">
            <label>Ảnh minh họa hiện tại</label>
            <div style="margin-bottom: 10px;">
                <img id="current_img" src="" style="height: 150px; border-radius: 5px; border: 1px solid #ddd;" onerror="this.src='<?= URLROOT ?>assets/images/default_news.png'">
            </div>
            <input type="file" name="hinhanh">
            <small class="form-hint">Chọn ảnh mới nếu muốn thay đổi.</small>
        </div>

        <div class="form-group">
            <label>Tóm tắt</label>
            <textarea name="tomtat" id="tomtat" rows="4" class="form-control"></textarea>
        </div>

        <div class="form-group">
            <label>Nội dung chi tiết</label>
            <textarea name="noidung" id="editor" rows="10" class="form-control"></textarea>
        </div>

        <div class="form-group">
            <label>Trạng thái</label>
            <select name="trangthai" id="trangthai" class="form-control"></select>
        </div>

        <div class="btn-group-center">
            <button type="submit" id="btnSubmit" class="btn btn-OK">💾 Cập nhật</button>
            <a href="<?= URLROOT ?>admin/news" class="btn btn-Cancel">❌ Hủy bỏ</a>
        </div>
    </form>
</div>

<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
<script>
    const BASE_URL = '<?= URLROOT ?>';
    const newsId = <?= $id ?? 0 ?>;
</script>
<script src="<?= URLROOT ?>assets/js/admin/news.js"></script>
