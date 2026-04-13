<link rel="stylesheet" href="<?= URLROOT ?>assets/css/site/auth.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

<div id="auth-modal" class="modal-overlay">
    <div class="modal-content">
        <span class="close-btn" onclick="closeAuthModal()">&times;</span>

        <div class="auth-tabs">
            <div class="tab-item active" onclick="switchTab('login')">Đăng nhập</div>
            <div class="tab-item" onclick="switchTab('register')">Đăng ký</div>
        </div>

        <form id="login-form" class="auth-form active">
            <div class="input-group">
                <label>TÊN ĐĂNG NHẬP (HOẶC EMAIL) *</label>
                <input type="text" id="login-username" placeholder="Nhập tên đăng nhập hoặc email" required>
            </div>
            <div class="input-group">
                <label>MẬT KHẨU *</label>
                <div class="password-field">
                    <input type="password" id="login-password" class="pass-input" placeholder="Nhập mật khẩu" required>
                    <i class="far fa-eye toggle-password"></i>
                </div>
            </div>
            <button type="submit" class="btn-submit">Đăng nhập</button>
        </form>

        <form id="register-form" class="auth-form">
            <div class="input-group">
                <label>HỌ VÀ TÊN *</label>
                <input type="text" id="reg-hoten" placeholder="Nhập họ và tên" required>
            </div>

            <div class="input-group">
                <label>TÊN ĐĂNG NHẬP *</label>
                <input type="text" id="reg-username" placeholder="Viết liền không dấu (VD: user123)" required>
            </div>

            <div class="input-group">
                <label>EMAIL (TÙY CHỌN)</label>
                <input type="email" id="reg-email" placeholder="Ví dụ: abc@gmail.com">
            </div>

            <div class="input-group">
                <label>MẬT KHẨU *</label>
                <div class="password-field">
                    <input type="password" id="reg-password" placeholder="Nhập mật khẩu" required>
                    <i class="far fa-eye toggle-password"></i>
                </div>
            </div>

            <div class="input-group">
                <label>NHẬP LẠI MẬT KHẨU *</label>
                <div class="password-field">
                    <input type="password" id="reg-confirm-password" placeholder="Nhập lại mật khẩu" required>
                    <i class="far fa-eye toggle-password"></i>
                </div>
            </div>

            <button type="submit" class="btn-submit">Đăng ký</button>
            <p class="auth-note">
                Bằng cách đăng ký tài khoản, bạn đồng ý với Điều khoản sử dụng của chúng tôi.
            </p>
        </form>
    </div>
</div>

<script>const AUTH_BASE_URL = '<?= URLROOT ?>';</script>
<script src="<?= URLROOT ?>assets/js/site/auth_modal.js"></script>
