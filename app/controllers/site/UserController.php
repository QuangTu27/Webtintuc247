<?php

class UserController extends Controller
{
    private function requireLogin(): void
    {
        if (!isset($_SESSION['client_logged_in']) || $_SESSION['client_logged_in'] !== true) {
            if ($this->isApiRequest()) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Vui lòng đăng nhập']);
                exit;
            }
            header('Location: ' . URLROOT);
            exit;
        }
    }

    private function isApiRequest(): bool
    {
        return $_SERVER['REQUEST_METHOD'] !== 'GET' ||
               isset($_SERVER['HTTP_X_REQUESTED_WITH']) ||
               (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
    }

    private function getUserId(): int
    {
        return (int)($_SESSION['client_id'] ?? 0);
    }

    private function getMenuItems(): array
    {
        return $this->model('CategoriesModel')->getAll();
    }

    /**
     * Hiển thị trang profile với tab được chọn.
     * URL: /user/profile, /user/profile/general, /user/profile/comments, ...
     */
    public function profile($tab = 'general')
    {
        $this->requireLogin();

        $validTabs = ['general', 'comments', 'bookmarks', 'history'];
        if (!in_array($tab, $validTabs)) {
            $tab = 'general';
        }

        $userId = $this->getUserId();
        $userModel = $this->model('UserModel');
        $user = $userModel->getById($userId);

        $data = [
            'menuItems'   => $this->getMenuItems(),
            'avatar'      => !empty($user->avatar) ? $user->avatar : 'default_avatar.svg',
            'displayName' => !empty($user->hoten) ? $user->hoten : ($user->username ?? 'Người dùng'),
            'username'    => $user->username ?? '',
            'activeTab'   => $tab
        ];

        $this->view('site/layouts/header', $data);
        $this->view('site/users/profile', $data);
        $this->view('site/layouts/footer');
    }


    // ===========================
    //         API ENDPOINTS
    // ===========================

    /** GET /user/info — Lấy thông tin cơ bản người dùng */
    public function info()
    {
        $this->requireLogin();
        header('Content-Type: application/json');

        $profileModel = $this->model('UserProfileModel');
        $user = $profileModel->getInfo($this->getUserId());

        if (!$user) {
            echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy người dùng']);
            exit;
        }

        echo json_encode(['status' => 'success', 'data' => $user]);
        exit;
    }

    /** POST /user/update-avatar — Cập nhật ảnh đại diện */
    public function updateAvatar()
    {
        $this->requireLogin();
        header('Content-Type: application/json');

        if (!isset($_FILES['avatar_file']) || $_FILES['avatar_file']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['status' => 'error', 'message' => 'Không có file ảnh được gửi lên']);
            exit;
        }

        $ext = strtolower(pathinfo($_FILES['avatar_file']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            echo json_encode(['status' => 'error', 'message' => 'Định dạng ảnh không hợp lệ']);
            exit;
        }

        $userId = $this->getUserId();
        $profileModel = $this->model('UserProfileModel');
        $oldRow = $profileModel->getAvatarById($userId);

        $uploadDir = dirname(APPROOT) . '/assets/images/avatars/';
        $filename = 'avatar_' . $userId . '_' . time() . '.' . $ext;

        if (!move_uploaded_file($_FILES['avatar_file']['tmp_name'], $uploadDir . $filename)) {
            echo json_encode(['status' => 'error', 'message' => 'Lỗi khi lưu file ảnh']);
            exit;
        }

        // Xóa ảnh cũ
        if ($oldRow && !empty($oldRow->avatar) && !in_array($oldRow->avatar, ['default_avatar.svg', 'default_avatar.png'])) {
            if (file_exists($uploadDir . $oldRow->avatar)) {
                unlink($uploadDir . $oldRow->avatar);
            }
        }

        $profileModel->updateAvatar($userId, $filename);
        $_SESSION['client_avatar'] = $filename;

        echo json_encode(['status' => 'success', 'message' => 'Cập nhật ảnh đại diện thành công!', 'avatar' => $filename]);
        exit;
    }

    /** POST /user/update-name — Cập nhật họ tên */
    public function updateName()
    {
        $this->requireLogin();
        header('Content-Type: application/json');

        $hoten = trim($_POST['hoten'] ?? '');
        if (empty($hoten)) {
            echo json_encode(['status' => 'error', 'message' => 'Họ tên không được để trống']);
            exit;
        }

        $userId = $this->getUserId();
        $this->model('UserProfileModel')->updateName($userId, $hoten);
        $_SESSION['client_hoten'] = $hoten;

        echo json_encode(['status' => 'success', 'message' => 'Đổi tên thành công!']);
        exit;
    }

    /** POST /user/update-email — Cập nhật email */
    public function updateEmail()
    {
        $this->requireLogin();
        header('Content-Type: application/json');

        $email = trim($_POST['email'] ?? '');
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['status' => 'error', 'message' => 'Email không hợp lệ']);
            exit;
        }

        $userId = $this->getUserId();
        $profileModel = $this->model('UserProfileModel');

        if ($profileModel->emailExistsForOther($email, $userId)) {
            echo json_encode(['status' => 'error', 'message' => 'Email này đã được sử dụng bởi tài khoản khác']);
            exit;
        }

        $profileModel->updateEmail($userId, $email);
        echo json_encode(['status' => 'success', 'message' => 'Cập nhật email thành công!']);
        exit;
    }

    /** POST /user/change-password — Đổi mật khẩu */
    public function changePassword()
    {
        $this->requireLogin();
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $oldPass = $input['old_password'] ?? '';
        $newPass = $input['new_password'] ?? '';
        $confirmPass = $input['confirm_password'] ?? '';

        if (empty($oldPass) || empty($newPass) || empty($confirmPass)) {
            echo json_encode(['status' => 'error', 'message' => 'Vui lòng điền đầy đủ thông tin']);
            exit;
        }

        if ($newPass !== $confirmPass) {
            echo json_encode(['status' => 'error', 'message' => 'Mật khẩu mới không khớp']);
            exit;
        }

        $userId = $this->getUserId();
        $profileModel = $this->model('UserProfileModel');
        $userRow = $profileModel->getPasswordById($userId);

        if (!$userRow || !password_verify($oldPass, $userRow->password)) {
            echo json_encode(['status' => 'error', 'message' => 'Mật khẩu cũ không chính xác']);
            exit;
        }

        $profileModel->updatePassword($userId, password_hash($newPass, PASSWORD_DEFAULT));
        echo json_encode(['status' => 'success', 'message' => 'Đổi mật khẩu thành công! Vui lòng đăng nhập lại.']);
        exit;
    }

    /** GET /user/history — Lịch sử tin đã xem */
    public function history()
    {
        $this->requireLogin();
        header('Content-Type: application/json');

        $userId = $this->getUserId();
        $items = $this->model('UserProfileModel')->getHistory($userId);
        echo json_encode(['status' => 'success', 'data' => $items]);
        exit;
    }

    /** POST /user/clear-history — Xóa lịch sử xem tin */
    public function clearHistory()
    {
        $this->requireLogin();
        header('Content-Type: application/json');

        $userId = $this->getUserId();
        $cookieName = 'viewed_news_' . $userId;
        setcookie($cookieName, '', time() - 3600, '/');
        echo json_encode(['status' => 'success', 'message' => 'Đã xóa lịch sử xem tin']);
        exit;
    }

    /** POST /user/delete-history-item — Xóa 1 bài khỏi lịch sử */
    public function deleteHistoryItem()
    {
        $this->requireLogin();
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $newsId = (int)($input['news_id'] ?? 0);

        $userId = $this->getUserId();
        $cookieName = 'viewed_news_' . $userId;
        $ids = isset($_COOKIE[$cookieName]) ? json_decode($_COOKIE[$cookieName], true) : [];
        $ids = array_values(array_filter((array)$ids, fn($id) => (int)$id !== $newsId));
        setcookie($cookieName, json_encode($ids), time() + (86400 * 30), '/');

        echo json_encode(['status' => 'success']);
        exit;
    }

    /** GET /user/bookmarks — Tin đã lưu */
    public function bookmarks()
    {
        $this->requireLogin();
        header('Content-Type: application/json');

        $items = $this->model('UserProfileModel')->getBookmarks($this->getUserId());
        echo json_encode(['status' => 'success', 'data' => $items]);
        exit;
    }

    /** POST /user/bookmark — Toggle bookmark (dùng từ trang chi tiết tin) */
    public function bookmark()
    {
        $this->requireLogin();
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $newsId = (int)($input['news_id'] ?? 0);

        if ($newsId <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'ID không hợp lệ']);
            exit;
        }

        $saved = $this->model('UserProfileModel')->toggleBookmark($this->getUserId(), $newsId);
        echo json_encode(['status' => 'success', 'action' => $saved ? 'saved' : 'unsaved']);
        exit;
    }

    /** POST /user/delete-bookmark — Bỏ lưu một bài */
    public function deleteBookmark()
    {
        $this->requireLogin();
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $newsId = (int)($input['news_id'] ?? 0);

        if ($newsId <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'ID không hợp lệ']);
            exit;
        }

        $this->model('UserProfileModel')->deleteBookmark($this->getUserId(), $newsId);
        echo json_encode(['status' => 'success', 'message' => 'Đã bỏ lưu bài viết']);
        exit;
    }

    /** GET /user/comments — Bình luận của tôi */
    public function comments()
    {
        $this->requireLogin();
        header('Content-Type: application/json');

        $items = $this->model('UserProfileModel')->getComments($this->getUserId());
        echo json_encode(['status' => 'success', 'data' => $items]);
        exit;
    }

    /** POST /user/delete-comment — Xóa bình luận */
    public function deleteComment()
    {
        $this->requireLogin();
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $commentId = (int)($input['comment_id'] ?? 0);

        if ($commentId <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'ID không hợp lệ']);
            exit;
        }

        $deleted = $this->model('UserProfileModel')->deleteComment($commentId, $this->getUserId());
        if ($deleted) {
            echo json_encode(['status' => 'success', 'message' => 'Đã xóa bình luận']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Bình luận không tồn tại hoặc không thuộc về bạn']);
        }
        exit;
    }
}
