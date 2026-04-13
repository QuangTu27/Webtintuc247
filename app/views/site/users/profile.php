<?php

$activeTab = $data['activeTab'] ?? 'general';
$avatarUrl = URLROOT . 'assets/images/avatars/' . ($data['avatar'] ?? 'default_avatar.png');
$username  = htmlspecialchars($data['username'] ?? '');
?>

<link rel="stylesheet" href="<?= URLROOT ?>assets/css/site/profile.css">

<div class="container profile-container">

    <!-- ===== SIDEBAR ===== -->
    <div class="profile-sidebar">
        <div class="user-card">
            <div class="user-card-header">
                <img src="<?= $avatarUrl ?>" class="avatar-circle" id="sidebar-avatar"
                     onerror="this.src='<?= URLROOT ?>assets/images/avatars/default_avatar.png'">
                <div class="user-meta">
                    <h4 id="sidebar-username"><?= $username ?></h4>
                    <span id="sidebar-join">Thành viên</span>
                </div>
            </div>

            <ul class="profile-menu">
                <li class="<?= $activeTab === 'general'   ? 'active' : '' ?>" id="menu-general">
                    <a href="<?= URLROOT ?>user/profile/general">Thông tin chung</a>
                </li>
                <li class="<?= $activeTab === 'comments'  ? 'active' : '' ?>" id="menu-comments">
                    <a href="<?= URLROOT ?>user/profile/comments">Ý kiến của bạn</a>
                </li>
                <li class="<?= $activeTab === 'bookmarks' ? 'active' : '' ?>" id="menu-bookmarks">
                    <a href="<?= URLROOT ?>user/profile/bookmarks">Tin đã lưu</a>
                </li>
                <li class="<?= $activeTab === 'history'   ? 'active' : '' ?>" id="menu-history">
                    <a href="<?= URLROOT ?>user/profile/history">Tin đã xem</a>
                </li>
                <li class="logout-item">
                    <a href="<?= URLROOT ?>auth/logout" id="logoutBtn">
                        Thoát <i class="fas fa-sign-out-alt"></i>
                    </a>
                </li>
            </ul>
        </div>

        <div class="support-box">
            Cần hỗ trợ, vui lòng liên hệ:<br>
            <a class="contact" href="mailto:bandoc@web24h.net">bandoc@web24h.net</a>
        </div>
    </div>

    <div class="profile-content" id="profile-main-content">
        <div class="loading-profile" style="text-align:center; padding:50px; color:#888; font-style:italic;">
            Đang tải...
        </div>
    </div>

</div>

<script>
    const BASE_URL   = '<?= URLROOT ?>';
    const ACTIVE_TAB = '<?= $activeTab ?>';
    const SITE_AVATAR_BASE = '<?= URLROOT ?>assets/images/avatars/';
    const SITE_NEWS_BASE   = '<?= URLROOT ?>assets/images/news/';
    const DEFAULT_AVATAR   = '<?= URLROOT ?>assets/images/avatars/default_avatar.png';
</script>
<script src="<?= URLROOT ?>assets/js/site/profile.js"></script>
