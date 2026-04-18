<?php

class AuthController extends Controller
{
    public function index()
    {
        $this->login();
    }
    
    public function login()
    {
        if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
            header('Location: ' . URLROOT . 'admin/dashboard');
            exit;
        }
        $this->view('admin/auth/login');
    }

    public function loginSubmit()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Lỗi phương thức kết nối']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $username = trim($data['username'] ?? '');
        $password = trim($data['password'] ?? '');

        if (empty($username) || empty($password)) {
            echo json_encode(['status' => 'error', 'message' => 'Vui lòng nhập đủ thông tin']);
            exit;
        }

        $authModel = $this->model('AuthModel'); 
        
        // Xét Admin
        $admin = $authModel->findAdminByCredentials($username, $password);
        if ($admin) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin->id;
            $_SESSION['admin_username'] = $admin->username;
            $_SESSION['admin_hoten'] = $admin->hoten;
            $_SESSION['admin_role'] = $admin->role;
            $_SESSION['admin_avatar'] = $admin->avatar;

            echo json_encode(['status' => 'success', 'redirect' => URLROOT . 'admin/dashboard']);
            exit;
        }

        // Xét User thường
        $user = $authModel->findUserByCredentials($username, $password);
        if ($user) {
            echo json_encode(['status' => 'error', 'message' => 'Tài khoản này không có quyền truy cập trang quản trị']);
            exit;
        }

        echo json_encode(['status' => 'error', 'message' => 'Tài khoản hoặc mật khẩu không đúng']);
        exit;
    }

    public function logout()
    {
        foreach ($_SESSION as $key => $value) {
            if (strpos($key, 'admin_') === 0) {
                unset($_SESSION[$key]);
            }
        }
        header('Location: ' . URLROOT . 'admin/auth/login');
        exit;
    }
}
