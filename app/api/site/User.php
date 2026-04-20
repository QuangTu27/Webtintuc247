<?php
/**
 * API Site UserController - Quản lý profile, bookmarks, history, comments của user site
 * Tất cả đều yêu cầu đăng nhập client (session client_logged_in)
 *
 * GET  /api/site/user/info       → Thông tin user
 * POST /api/site/user/avatar     → Cập nhật ảnh đại diện (file upload)
 * POST /api/site/user/name       → Cập nhật họ tên
 * POST /api/site/user/email      → Cập nhật email
 * POST /api/site/user/password   → Đổi mật khẩu
 * GET  /api/site/user/history    → Lịch sử xem tin
 * POST /api/site/user/history    → Xoá lịch sử (action=clear|delete_item)
 * GET  /api/site/user/bookmarks  → Tin đã lưu
 * DELETE /api/site/user/bookmarks → Xoá bookmark (body: {news_id})
 * GET  /api/site/user/comments   → Bình luận của tôi
 * DELETE /api/site/user/comments → Xoá bình luận (body: {comment_id})
 */
class User extends ApiController
{
    public function __construct()
    {
        $this->requireUser();
    }

    private function getUserId(): int
    {
        return (int)($_SESSION['client_id'] ?? 0);
    }

    // GET /api/site/user 
    public function index()
    {
        $this->info();
    }

    // GET /api/site/user/info
    public function info()
    {
        $profileModel = $this->model('UserProfileModel');
        $user = $profileModel->getInfo($this->getUserId());
        if (!$user) {
            $this->json('error', 'Không tìm thấy người dùng');
        }
        $this->json('success', $user);
    }

    // POST /api/site/user/avatar
    public function avatar()
    {
        if (!isset($_FILES['avatar_file']) || $_FILES['avatar_file']['error'] !== UPLOAD_ERR_OK) {
            $this->json('error', 'Không có file ảnh được gửi lên');
        }

        $ext = strtolower(pathinfo($_FILES['avatar_file']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            $this->json('error', 'Định dạng ảnh không hợp lệ');
        }

        $userId       = $this->getUserId();
        $profileModel = $this->model('UserProfileModel');
        $oldRow       = $profileModel->getAvatarById($userId);
        $uploadDir    = dirname(APPROOT) . '/assets/images/avatars/';
        $filename     = 'avatar_' . $userId . '_' . time() . '.' . $ext;

        if (!move_uploaded_file($_FILES['avatar_file']['tmp_name'], $uploadDir . $filename)) {
            $this->json('error', 'Lỗi khi lưu file ảnh');
        }

        if ($oldRow && !empty($oldRow->avatar) && !in_array($oldRow->avatar, ['default_avatar.svg', 'default_avatar.png'])) {
            if (file_exists($uploadDir . $oldRow->avatar)) {
                unlink($uploadDir . $oldRow->avatar);
            }
        }

        $profileModel->updateAvatar($userId, $filename);
        $_SESSION['client_avatar'] = $filename;

        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'Cập nhật ảnh thành công!', 'avatar' => $filename]);
        exit;
    }

    // POST /api/site/user/name
    public function name()
    {
        $hoten = trim($_POST['hoten'] ?? '');
        if (empty($hoten)) { $this->json('error', 'Họ tên không được để trống'); }

        $userId = $this->getUserId();
        $this->model('UserProfileModel')->updateName($userId, $hoten);
        $_SESSION['client_hoten'] = $hoten;
        $this->json('success', 'Đổi tên thành công!');
    }

    // POST /api/site/user/email
    public function email()
    {
        $email = trim($_POST['email'] ?? '');
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->json('error', 'Email không hợp lệ');
        }

        $userId       = $this->getUserId();
        $profileModel = $this->model('UserProfileModel');
        if ($profileModel->emailExistsForOther($email, $userId)) {
            $this->json('error', 'Email này đã được sử dụng bởi tài khoản khác');
        }

        $profileModel->updateEmail($userId, $email);
        $this->json('success', 'Cập nhật email thành công!');
    }

    // POST /api/site/user/password
    public function password()
    {
        $input       = json_decode(file_get_contents('php://input'), true) ?? [];
        $oldPass     = $input['old_password'] ?? '';
        $newPass     = $input['new_password'] ?? '';
        $confirmPass = $input['confirm_password'] ?? '';

        if (empty($oldPass) || empty($newPass) || empty($confirmPass)) {
            $this->json('error', 'Vui lòng điền đầy đủ thông tin');
        }
        if ($newPass !== $confirmPass) {
            $this->json('error', 'Mật khẩu mới không khớp');
        }

        $userId       = $this->getUserId();
        $profileModel = $this->model('UserProfileModel');
        $userRow      = $profileModel->getPasswordById($userId);

        if (!$userRow || !password_verify($oldPass, $userRow->password)) {
            $this->json('error', 'Mật khẩu cũ không chính xác');
        }

        $profileModel->updatePassword($userId, password_hash($newPass, PASSWORD_DEFAULT));
        $this->json('success', 'Đổi mật khẩu thành công! Vui lòng đăng nhập lại.');
    }

    // GET /api/site/user/history | DELETE /api/site/user/history
    public function history()
    {
        $userId = $this->getUserId();
        if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            $input      = json_decode(file_get_contents('php://input'), true) ?? [];
            $action     = $input['action'] ?? 'clear';
            $cookieName = 'viewed_news_' . $userId;

            if ($action === 'delete_item') {
                $newsId = (int)($input['news_id'] ?? 0);
                $ids    = isset($_COOKIE[$cookieName]) ? json_decode($_COOKIE[$cookieName], true) : [];
                $ids    = array_values(array_filter((array)$ids, fn($id) => (int)$id !== $newsId));
                setcookie($cookieName, json_encode($ids), time() + (86400 * 30), '/');
            } else {
                setcookie($cookieName, '', time() - 3600, '/');
            }
            $this->json('success', 'Đã xoá lịch sử');
        }

        $items = $this->model('UserProfileModel')->getHistory($userId);
        $this->json('success', $items);
    }

    // GET /api/site/user/bookmarks | DELETE /api/site/user/bookmarks
    public function bookmarks()
    {
        $userId = $this->getUserId();
        if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            $input  = json_decode(file_get_contents('php://input'), true) ?? [];
            $newsId = (int)($input['news_id'] ?? 0);
            if ($newsId <= 0) { $this->json('error', 'ID không hợp lệ'); }
            $this->model('UserProfileModel')->deleteBookmark($userId, $newsId);
            $this->json('success', 'Đã bỏ lưu bài viết');
        }

        $items = $this->model('UserProfileModel')->getBookmarks($userId);
        $this->json('success', $items);
    }

    // GET /api/site/user/comments | DELETE /api/site/user/comments
    public function comments()
    {
        $userId = $this->getUserId();
        if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            $input     = json_decode(file_get_contents('php://input'), true) ?? [];
            $commentId = (int)($input['comment_id'] ?? 0);
            if ($commentId <= 0) { $this->json('error', 'ID không hợp lệ'); }
            $deleted = $this->model('UserProfileModel')->deleteComment($commentId, $userId);
            if ($deleted) { $this->json('success', 'Đã xóa bình luận'); }
            $this->json('error', 'Bình luận không tồn tại hoặc không thuộc về bạn');
        }

        $items = $this->model('UserProfileModel')->getComments($userId);
        $this->json('success', $items);
    }
}

