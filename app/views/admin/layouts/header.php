<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ thống Quản trị Tin Tức</title>
    <link rel="stylesheet" href="<?= URLROOT ?>assets/css/admin/style_admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>

    <script src="<?= URLROOT ?>assets/js/admin/header.js"></script>
</head>
<body>
    <div class="wrapper">
        <nav class="sidebar">
            <div class="brand">
                <h2>ADMIN PANEL</h2>
            </div>
            <ul class="menu">
                <li><a href="<?= URLROOT ?>admin/dashboard"><i class="fas fa-home"></i> Trang chủ</a></li>
                <li><a href="<?= URLROOT ?>admin/news"><i class="fas fa-newspaper"></i> Quản lý Tin tức</a></li>
                <li><a href="<?= URLROOT ?>admin/categories"><i class="fas fa-list"></i> Quản lý Danh mục</a></li>
                <li><a href="<?= URLROOT ?>admin/comments"><i class="fas fa-comments"></i> Quản lý Bình luận</a></li>
                <li><a href="<?= URLROOT ?>admin/ads"><i class="fas fa-ad"></i> Quản lý Quảng cáo</a></li>
                <li><a href="<?= URLROOT ?>admin/users"><i class="fas fa-users"></i> Quản lý Tài khoản</a></li>
            </ul>

            <div class="user-dropdown">
                <div class="user-info" id="userInfoToggle">
                    <?php 
                        $avatar = $_SESSION['admin_avatar'] ?? '';
                        $avatarUrl = !empty($avatar) ? URLROOT . "assets/images/avatars/" . $avatar : URLROOT . "assets/images/avatars/default_avatar.svg";
                        
                        $name = $_SESSION['admin_hoten'] ?? $_SESSION['admin_username'] ?? 'Khách';
                        $role = $_SESSION['admin_role'] ?? '';

                        $roleText = match($role) {
                            'admin'     => 'Admin',
                            'phongvien' => 'Phóng Viên',
                            'editor'    => 'Biên Tập Viên',
                            'nhabao'    => 'Nhà Báo',
                            'ctv'       => 'Cộng Tác Viên',
                            default     => 'Người dùng'
                        };
                    ?>
                    <img id="headerAvatar" src="<?= $avatarUrl ?>" alt="Avatar">
                    <div class="user-text">
                        <span class="user-name" id="headerNameSidebar"><?= htmlspecialchars($name) ?></span>
                        <small class="user-role" id="headerRoleSidebar"><?= htmlspecialchars($roleText) ?></small>
                    </div>
                    <i class="fas fa-caret-up"></i>
                </div>

                <ul class="user-menu" id="userMenu" style="display: none;">
                    <li>
                        <a href="<?= URLROOT ?>admin/profile">
                            <i class="fas fa-user"></i> Thông tin cá nhân
                        </a>
                    </li>
                    <li>
                        <a href="<?= URLROOT ?>admin/auth/logout" class="logout-link" id="logoutLink">
                            <i class="fas fa-sign-out-alt"></i> Đăng xuất
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="main-content">
            <header class="top-bar">
                <span id="headerNameTopbar">Xin chào, <b><?= htmlspecialchars($name) ?></b></span>
            </header>

            <div class="content-body">
