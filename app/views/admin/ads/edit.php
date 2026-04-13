<div class="admin-container">
    <h2 class="admin-title">CẬP NHẬT QUẢNG CÁO</h2>

    <div id="status-msg" style="display:none; padding:10px; margin-bottom:15px; border-radius:4px;" class="alert alert-warning"></div>
    <div id="loading" style="text-align:center; padding: 20px;">Đang tải dữ liệu...</div>

    <form id="editForm" class="admin-form" style="display: none;">
        <input type="hidden" id="adId" value="<?= $id ?? 0 ?>">

        <div class="form-group">
            <label>Tiêu đề</label>
            <input type="text" id="title" required>
        </div>

        <div class="form-group">
            <label>Media hiện tại</label>
            <div id="currentMedia" style="margin-bottom: 10px;"></div>
            <label>Chọn file mới (Ảnh hoặc Video - Bỏ qua nếu không muốn đổi)</label>
            <input type="file" id="image_file" accept="image/*,video/mp4">
        </div>

        <div class="form-group">
            <label>Link liên kết</label>
            <input type="text" id="link" required>
        </div>

        <div class="form-group">
            <label>Vị trí hiển thị</label>
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
                <option value="an">Ẩn</option>
            </select>
        </div>

        <div class="btn-group-center">
            <button type="submit" class="btn btn-OK">💾 Cập nhật</button>
            <a href="<?= URLROOT ?>admin/ads" class="btn btn-Cancel">❌ Huỷ</a>
        </div>
    </form>
</div>

<script>
    const BASE_URL = '<?= URLROOT ?>';
    const adId = <?= $id ?? 0 ?>;
</script>
<script src="<?= URLROOT ?>assets/js/admin/ads.js"></script>
