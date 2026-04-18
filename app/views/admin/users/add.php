<div class="admin-container">
    <h2 class="admin-title">Thêm người dùng</h2>

    <div id="status-msg" style="display:none; margin-bottom: 15px; padding: 10px; border-radius: 5px;"></div>

    <form id="addForm" class="admin-form">
        <div class="form-group">
            <label>Username</label>
            <input type="text" id="username" placeholder="Nhập tên đăng nhập (ví dụ: admin_24h)" required>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" id="password" placeholder="Nhập mật khẩu" required>
            <small class="form-hint">*Mật khẩu nên bao gồm cả chữ cái và chữ số.*</small>
        </div>

        <div class="form-group">
            <label>Họ tên</label>
            <input type="text" id="hoten" placeholder="Nhập họ và tên đầy đủ..." required>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" id="email" placeholder="vidu@gmail.com">
        </div>

        <div class="form-group">
            <label>Quyền</label>
            <select id="role">
                <option value="editor">Editor</option>
                <option value="phongvien">Phóng viên</option>
                <option value="nhabao">Nhà báo</option>
                <option value="ctv">Cộng tác viên</option>
                <option value="user" selected>User</option>
            </select>
        </div>

        <div class="btn-group-center">
            <button type="submit" class="btn btn-OK">💾 Lưu</button>
            <a href="<?= URLROOT ?>admin/users" class="btn btn-Cancel">❌ Huỷ</a>
        </div>
    </form>
</div>

<script>
    const BASE_URL = '<?= URLROOT ?>';
    const USER_ID = 0; // Trang Add
</script>
<script src="<?= URLROOT ?>assets/js/admin/users.js"></script>
