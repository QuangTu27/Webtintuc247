<?php

class ProfileController extends Controller
{
    public function __construct() {
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            header('Location: ' . URLROOT . 'admin/auth/login'); 
            exit;
        }
    }

    public function index()
    {
        $this->view('admin/layouts/header');
        $this->view('admin/profile');
        $this->view('admin/layouts/footer');
    }

    public function data()
    {
        header('Content-Type: application/json');
        
        $userId = (int)($_SESSION['admin_id'] ?? 0);
        if ($userId <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Người dùng chưa đăng nhập hợp lệ']);
            exit;
        }

        $profileModel = $this->model('AdminProfileModel');
        $user = $profileModel->getById($userId);

        if ($user) {
            echo json_encode(['status' => 'success', 'data' => $user]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy dữ liệu']);
        }
    }

    public function update()
    {
        header('Content-Type: application/json');
        
        $userId = (int)($_SESSION['admin_id'] ?? 0);
        if ($userId <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Phiên đăng nhập không hợp lệ']);
            exit;
        }

        $hoten = trim($_POST['hoten'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($hoten)) {
            echo json_encode(['status' => 'error', 'message' => 'Họ tên không được để trống']);
            exit;
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
                echo json_encode(['status' => 'error', 'message' => 'Ảnh đại diện không hợp lệ']);
                exit;
            }
            $avatarName = time() . '_' . rand(1000, 9999) . '.' . $ext;
            $uploadDir = dirname(APPROOT) . '/assets/images/avatars/';
            
            // Xoá ảnh cũ nếu không phải ảnh mặc định
            if (!empty($currentUser->avatar) && $currentUser->avatar !== 'default_avatar.svg') {
                $oldPath = $uploadDir . $currentUser->avatar;
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            move_uploaded_file($tmpName, $uploadDir . $avatarName);
            $_SESSION['admin_avatar'] = $avatarName; // Cập nhật session avatar
        }

        // Cập nhật lại session thông tin
        $_SESSION['admin_hoten'] = $hoten;
        
        $passToUpdate = !empty($password) ? $password : null;
        
        // Cập nhật Database: update($userId, $hoten, $email, $avatar, $password)
        $profileModel->update($userId, $hoten, $email, $avatarName, $passToUpdate);

        $response = ['status' => 'success', 'message' => 'Cập nhật thành công'];
        if ($avatarName) {
            $response['avatar'] = $avatarName;
        }
        echo json_encode($response);
        exit;
    }
}
