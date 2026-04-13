<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang tin tức 24H</title>
    <link rel="stylesheet" href="<?= URLROOT ?>assets/css/site/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>

<body>
    <?php require_once APPROOT . '/views/site/layouts/auth_modal.php'; ?>
    <div id="header-placeholder"></div>
    <header id="siteHeader">
        <div class="top-bar">
            <div class="container top-bar-inner">
                <a href="<?= URLROOT ?>" class="logo-top">TINTUC<span>24/7</span></a>

                <div class="weather-box">
                    <div class="date-location">
                        <div class="city" id="location">Hà Nội</div>
                        <div class="date" id="date-time">--</div>
                    </div>
                    <div class="weather-divider"></div>
                    <div class="weather-detail">
                        <img id="weather-icon" src="" alt="weather icon">
                        <span class="temp" id="temperature">--°C</span>
                    </div>
                </div>
                <div class="flex-spacer"></div>

                <div class="search-box" style="margin-right: 20px;">
                    <form action="<?= URLROOT ?>search" method="GET" style="display: flex; align-items: center; background: #fff; border: 1px solid #ccc; border-radius: 20px; padding: 4px 15px; box-shadow: inset 0 1px 3px rgba(0,0,0,0.05);">
                        <input type="text" name="keyword" placeholder="Tìm kiếm tin tức..." required style="border: none; background: transparent; outline: none; font-size: 14px; width: 180px; padding: 2px 5px;">
                        <button type="submit" style="border: none; background: transparent; color: #888; cursor: pointer; padding: 0;"><i class="fas fa-search"></i></button>
                    </form>
                </div>

                <div class="user-action">
                    <?php if (isset($_SESSION['client_logged_in'])): 
                        $siteAvatar = !empty($_SESSION['client_avatar']) ? $_SESSION['client_avatar'] : 'default_avatar.png';
                        $siteName = !empty($_SESSION['client_hoten']) ? $_SESSION['client_hoten'] : ($_SESSION['client_username'] ?? 'Người dùng');
                        $siteUsername = $_SESSION['client_username'] ?? '';
                    ?>
                        <div class="user-profile-box">
                            <div class="profile-toggle">
                                <img src="<?= URLROOT ?>assets/images/avatars/<?= htmlspecialchars($siteAvatar) ?>" class="user-avatar-mini" alt="User" onerror="this.src='<?= URLROOT ?>assets/images/avatars/default_avatar.png'">
                                <span class="name-text"><?= htmlspecialchars($siteName) ?></span>
                                <i class="fas fa-caret-down" style="font-size: 12px; color: white;"></i>
                            </div>

                            <ul class="profile-dropdown">
                                <li class="dropdown-header">
                                    <img src="<?= URLROOT ?>assets/images/avatars/<?= htmlspecialchars($siteAvatar) ?>" class="avatar-large" onerror="this.src='<?= URLROOT ?>assets/images/avatars/default_avatar.png'">
                                    <strong class="user-name-text"><?= htmlspecialchars($siteUsername) ?></strong>
                                </li>

                                <li><a href="<?= URLROOT ?>user/profile/general">Thông tin chung</a></li>
                                <li><a href="<?= URLROOT ?>user/profile/comments">Ý kiến của bạn</a></li>
                                <li><a href="<?= URLROOT ?>user/profile/bookmarks">Tin đã lưu</a></li>
                                <li><a href="<?= URLROOT ?>user/profile/history">Tin đã xem</a></li>

                                <li class="divider"></li>

                                <li>
                                    <a href="<?= URLROOT ?>auth/logout" class="logout-link" onclick="return confirm('Bạn muốn đăng xuất?')">
                                        Thoát <i class="fas fa-sign-out-alt"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="javascript:void(0)" onclick="openAuthModal('login')"><i class="fas fa-user"></i> Đăng nhập</a>
                        <span style="margin: 0 5px;">|</span>
                        <a href="javascript:void(0)" onclick="openAuthModal('register')">Đăng ký</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="nav-menu-wrapper">
            <div class="container nav-inner">

                <a href="<?= URLROOT ?>" class="nav-btn-home">
                    <i class="fas fa-home"></i>
                </a>

                <nav class="nav-categories">
                    <ul>
                        <?php
                        $count = 0;
                        foreach ($data['menuItems'] as $cat):
                            if ($cat->parent_id == 0):
                                $childCats = array_filter($data['menuItems'], fn($m) => $m->parent_id == $cat->id);
                                $hasChild = count($childCats) > 0;
                                if ($count < 9):
                        ?>
                                    <li class="<?= $hasChild ? 'has-child' : '' ?>">
                                        <a href="<?= URLROOT ?>categories/<?= $cat->id ?>">
                                            <?= htmlspecialchars($cat->name) ?>
                                        </a>

                                        <?php if ($hasChild): ?>
                                            <ul class="sub-menu">
                                                <?php foreach ($childCats as $sub): ?>
                                                    <li>
                                                        <a href="<?= URLROOT ?>categories/<?= $sub->id ?>">
                                                            <?= htmlspecialchars($sub->name) ?>
                                                        </a>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php endif; ?>
                                    </li>
                        <?php
                                endif;
                                $count++;
                            endif;
                        endforeach;
                        ?>
                    </ul>
                </nav>

                <a href="javascript:void(0)" id="btnOpenMenu" class="nav-btn-all">
                    <i class="fas fa-bars" title="Tất cả chuyên mục"></i>
                </a>

            </div>
        </div>
    </header>

    <div id="full-menu-overlay">
        <div class="container">
            <div class="full-menu-header">
                <h3>Tất cả chuyên mục</h3>
                <span id="btnCloseMenu" class="close-btn"> <i class="fas fa-times"></i></span>
            </div>

            <div class="full-menu-grid">
                <?php
                foreach ($data['menuItems'] as $cat):
                    if ($cat->parent_id == 0):
                        $childCats = array_filter($data['menuItems'], fn($m) => $m->parent_id == $cat->id);
                ?>
                        <div class="menu-column">
                            <a href="<?= URLROOT ?>categories/<?= $cat->id ?>" class="parent-link">
                                <?= htmlspecialchars($cat->name) ?>
                            </a>

                            <?php if (count($childCats) > 0): ?>
                                <div class="child-links">
                                    <?php foreach ($childCats as $sub): ?>
                                        <a href="<?= URLROOT ?>categories/<?= $sub->id ?>">
                                            <?= htmlspecialchars($sub->name) ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                <?php
                    endif;
                endforeach;
                ?>
            </div>
        </div>
    </div>

    <script src="<?= URLROOT ?>assets/js/site/header.js"></script>
