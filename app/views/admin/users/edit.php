<div class="admin-container">
    <h2 class="admin-title">Cập nhật người dùng</h2>

    <div id="status-msg" style="display:none; margin-bottom: 15px; padding: 10px; border-radius: 5px;"></div>
    <div id="loadingMsg" style="text-align:center; padding: 30px;">🔄 Tải dữ liệu từ API...</div>

    <form id="editForm" class="admin-form" style="display:none;">
        <div class="form-group">
            <label>Username</label>
            <input type="text" id="username" disabled>
        </div>

        <div class="form-group">
            <label>Mật khẩu mới (Bỏ trống nếu không đổi)</label>
            <input type="password" id="password">
        </div>

        <div class="form-group">
            <label>Họ tên</label>
            <input type="text" id="hoten" required>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" id="email" required>
        </div>

        <div class="form-group">
            <label>Quyền</label>
            <select id="role">
                <option value="user">User</option>
                <option value="editor">Editor</option>
                <option value="phongvien">Phóng viên</option>
                <option value="nhabao">Nhà báo</option>
                <option value="ctv">Cộng tác viên</option>
            </select>
        </div>

        <div class="btn-group-center">
            <button type="submit" class="btn btn-OK">💾 Cập nhật</button>
            <a href="<?= URLROOT ?>admin/users" class="btn btn-Cancel">❌ Huỷ</a>
        </div>
    </form>
</div>

<script>
    const BASE_URL = '<?= URLROOT ?>';
    const USER_ID = <?= $id ?? 0 ?>;
</script>
<script src="<?= URLROOT ?>assets/js/admin/users.js"></script>
