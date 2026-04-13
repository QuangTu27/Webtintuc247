<div class="admin-container">
    <div id="status-msg" style="display:none; padding:10px; margin-bottom:15px; border-radius:4px;" class="alert alert-success"></div>

    <h2 class="admin-title">Thông tin cá nhân</h2>

    <div id="loading" style="text-align:center; padding: 20px;">Đang tải thông tin cá nhân...</div>

    <form id="profileForm" class="admin-form" style="display: none;">
        <div class="form-group text-center">
            <img id="avatarPreview" src="<?= URLROOT ?>assets/images/avatars/default_avatar.svg" class="admin-avatar">
            <input type="file" id="avatar" accept="image/*">
        </div>

        <div class="form-group">
            <label>Username</label>
            <input type="text" id="username" disabled>
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
            <label>Mật khẩu mới</label>
            <input type="password" id="password" placeholder="Để trống nếu không đổi">
        </div>

        <div class="form-group">
            <label>Quyền</label>
            <input type="text" id="role" disabled>
        </div>

        <div class="form-group">
            <label>Ngày tham gia</label>
            <input type="text" id="created_at" disabled>
        </div>

        <div class="btn-group-center">
            <button type="submit" class="btn btn-OK">💾 Lưu thay đổi</button>
            <a href="<?= URLROOT ?>admin/dashboard" class="btn btn-Cancel">❌ Huỷ</a>
        </div>
    </form>
</div>

<script>
    const BASE_URL = '<?= URLROOT ?>';
</script>
<script src="<?= URLROOT ?>assets/js/admin/profile.js"></script>
