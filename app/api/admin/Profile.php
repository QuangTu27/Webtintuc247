<?php

class Profile extends ApiController
{
    public function __construct() {
        $this->requireAdmin();
    }

    // GET /api/profile
    public function index()
    {
        $userId = (int)($_SESSION['admin_id'] ?? 0);
        if ($userId <= 0) {
            $this->json('error', 'Người dùng chưa đăng nhập hợp lệ');
        }

        $profileModel = $this->model('AdminProfileModel');
        $user = $profileModel->getById($userId);

        if (!$user) {
            $this->json('error', 'Không tìm thấy dữ liệu');
        }

        $this->json('success', $user);
    }

    // POST /api/profile (store → update profile vì không có ID riêng, userId lấy từ session)
    public function store()
    {
        $this->updateProfile();
    }

    // POST /api/profile/{id} (update)
    public function update($id = 0)
    {
        $this->updateProfile();
    }

    private function updateProfile()
    {
        $userId = (int)($_SESSION['admin_id'] ?? 0);
        if ($userId <= 0) {
            $this->json('error', 'Phiên đăng nhập không hợp lệ');
        }

        $hoten = trim($_POST['hoten'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($hoten)) {
            $this->json('error', 'Họ tên không được để trống');
        }

        $profileModel = $this->model('AdminProfileModel');
        $currentUser = $profileModel->getById($userId);

        $avatarName = null;

        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $tmpName = $_FILES['avatar']['tmp_name'];
            $fileInfo = pathinfo($_FILES['avatar']['name']);
            $ext = strtolower($fileInfo['extension']);
            $allowedExts = ['jpg', 'jpeg', 'png', 'svg', 'gif', 'webp'];
            if (!in_array($ext, $allowedExts)) {
                $this->json('error', 'Ảnh đại diện không hợp lệ');
            }
            $avatarName = time() . '_' . rand(1000, 9999) . '.' . $ext;
            $uploadDir = dirname(APPROOT) . '/assets/images/avatars/';

            if (!empty($currentUser->avatar) && $currentUser->avatar !== 'default_avatar.svg') {
                $oldPath = $uploadDir . $currentUser->avatar;
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            move_uploaded_file($tmpName, $uploadDir . $avatarName);
            $_SESSION['admin_avatar'] = $avatarName;
        }

        $_SESSION['admin_hoten'] = $hoten;

        $passToUpdate = !empty($password) ? $password : null;
        $profileModel->update($userId, $hoten, $email, $avatarName, $passToUpdate);

        $response = ['status' => 'success', 'data' => 'Cập nhật thành công'];
        if ($avatarName) {
            $response['avatar'] = $avatarName;
        }
        $this->jsonRaw($response);
    }
}

