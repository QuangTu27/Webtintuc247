<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập Admin</title>
    <link rel="stylesheet" href="<?= URLROOT ?>assets/css/admin/login.css">
</head>
<body class="login-page">

    <div class="login-box">
        <h2>QUẢN TRỊ VIÊN</h2>
        
        <div id="error-msg" class="error" style="display:none;"></div>

        <form id="loginForm">
            <input type="text" id="username" placeholder="Tên đăng nhập" required>
            <input type="password" id="password" placeholder="Mật khẩu" required>
            <button type="submit" id="btnLogin">ĐĂNG NHẬP</button>
        </form>
    </div>

    <script>
        const BASE_URL = '<?= URLROOT ?>';
    </script>
    <script src="<?= URLROOT ?>assets/js/admin/login.js"></script>
</body>
</html>