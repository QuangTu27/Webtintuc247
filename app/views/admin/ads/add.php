<h2 class="admin-title">THÊM QUẢNG CÁO</h2>

<div id="status-msg" style="display:none; padding:10px; margin-bottom:15px; border-radius:4px;" class="alert alert-warning"></div>

<form id="addForm" class="admin-form">
    <div class="form-group">
        <label>Tiêu đề</label>
        <input type="text" id="title" placeholder="Nhập tên quảng cáo..." required>
    </div>

    <div class="form-group">
        <label>Media (Hình ảnh hoặc Video)</label>
        <input type="file" id="media_file" accept="image/*,video/mp4" required>
        <small style="color: #666;">Hỗ trợ: .jpg, .png, .gif, .mp4</small>
    </div>

    <div class="form-group">
        <label>Link</label>
        <input type="text" id="link" placeholder="https://...">
    </div>

    <div class="form-group">
        <label>Vị trí</label>
        <select id="position">
            <option value="top_home">Đầu trang (top_home)</option>
            <option value="sidebar_left">Cột trái (sidebar_left)</option>
            <option value="sidebar_right">Cột phải (sidebar_right)</option>
            <option value="inline_home">Giữa nội dung (inline_home)</option>
            <option value="footer_home">Cuối trang (footer_home)</option>
        </select>
    </div>

    <div class="form-group">
        <label>Trạng thái</label>
        <select id="status">
            <option value="hien">Hiển thị</option>
            <option value="an" selected>Ẩn</option>
        </select>
    </div>

    <div class="btn-group-center">
        <button type="submit" class="btn btn-OK">💾 Lưu</button>
        <a href="<?= URLROOT ?>admin/ads" class="btn btn-Cancel">❌ Huỷ</a>
    </div>
</form>

<script>
    const BASE_URL = '<?= URLROOT ?>';
</script>
<script src="<?= URLROOT ?>assets/js/admin/ads.js"></script>
