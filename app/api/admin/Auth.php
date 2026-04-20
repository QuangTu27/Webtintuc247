<?php
class Auth extends ApiController
{
    // POST /api/auth (store) → login
    public function store()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $username = trim($data['username'] ?? '');
        $password = trim($data['password'] ?? '');

        if (empty($username) || empty($password)) {
            $this->json('error', 'Vui lòng nhập đầy đủ thông tin');
        }

        $authModel = $this->model('AuthModel');

        $admin = $authModel->findAdminByCredentials($username, $password);
        if ($admin) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin->id;
            $_SESSION['admin_username'] = $admin->username;
            $_SESSION['admin_hoten'] = $admin->hoten;
            $_SESSION['admin_role'] = $admin->role;
            $_SESSION['admin_avatar'] = $admin->avatar;

            $this->json('success', ['redirect' => URLROOT . 'admin/dashboard']);
        }

        $user = $authModel->findUserByCredentials($username, $password);
        if ($user) {
            $this->json('error', 'Tài khoản này không có quyền truy cập trang quản trị');
        }

        $this->json('error', 'Tài khoản hoặc mật khẩu không đúng');
    }

    // POST /api/auth/login 
    public function login()
    {
        $this->store();
    }

    // POST /api/auth/logout
    public function logout()
    {
        foreach ($_SESSION as $key => $value) {
            if (strpos($key, 'admin_') === 0) {
                unset($_SESSION[$key]);
            }
        }
        $this->json('success', ['redirect' => URLROOT . 'admin/auth/login']);
    }
}

