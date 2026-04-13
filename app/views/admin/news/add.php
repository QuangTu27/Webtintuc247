<div class="admin-container">
    <div class="admin-header-inline">
        <h2 class="admin-title">THÊM BÀI VIẾT MỚI</h2>
    </div>

    <div id="status-msg" style="display:none; padding:10px; margin-bottom:15px; border-radius:4px;" class="alert alert-warning"></div>

    <form id="addNewsForm" enctype="multipart/form-data" class="admin-form">
        <div class="form-group">
            <label>Tiêu đề bài viết</label>
            <input type="text" name="tieude" id="tieude" required placeholder="Nhập tiêu đề tin tức...">
        </div>

        <div class="form-group">
            <label>Danh mục</label>
            <select name="danhmuc" id="danhmuc" class="form-control" required>
                <option value="">-- Đang tải danh mục --</option>
            </select>
        </div>

        <div class="form-group">
            <label>Ảnh đại diện (Thumbnail)</label>
            <input type="file" name="hinhanh" id="hinhanh" required>
        </div>

        <div class="form-group">
            <label>Tóm tắt (Sapo)</label>
            <textarea name="tomtat" id="tomtat" rows="4" class="form-control" placeholder="Mô tả ngắn về bài viết..."></textarea>
        </div>

        <div class="form-group">
            <label>Nội dung chi tiết</label>
            <textarea name="noidung" id="editor" rows="10" class="form-control"></textarea>
        </div>

        <div class="form-group">
            <label>Trạng thái đăng</label>
            <select name="trangthai" id="trangthai" class="form-control">
                <option value="cho_duyet">⏳ Đang tải (kiểm tra quyền...)</option>
            </select>
            <small id="status-hint" class="form-hint" style="color:red; display:none;">* Bài viết của bạn cần được Biên tập viên duyệt trước khi hiển thị.</small>
        </div>

        <div class="btn-group-center">
            <button type="submit" id="btnSubmit" class="btn btn-OK">💾 Lưu bài viết</button>
            <a href="<?= URLROOT ?>admin/news" class="btn btn-Cancel">❌ Hủy bỏ</a>
        </div>
    </form>
</div>

<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
<script>
    const BASE_URL = '<?= URLROOT ?>';
</script>
<script src="<?= URLROOT ?>assets/js/admin/news.js"></script>
