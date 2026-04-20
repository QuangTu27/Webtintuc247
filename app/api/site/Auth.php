<?php

class Auth extends ApiController
{
    // POST /api/site/auth 
    public function store()
    {
        $this->login();
    }

    public function login()
    {
        $data     = json_decode(file_get_contents('php://input'), true) ?? [];
        $username = trim($data['username'] ?? '');
        $password = trim($data['password'] ?? '');

        if (empty($username) || empty($password)) {
            $this->json('error', 'Vui lòng nhập đủ thông tin');
        }

        $authModel = $this->model('AuthModel');

        $admin = $authModel->findAdminByCredentials($username, $password);
        if ($admin) {
            $this->json('error', 'Tài khoản admin vui lòng đăng nhập qua trang quản trị');
        }

        $user = $authModel->findUserByCredentials($username, $password);
        if ($user) {
            $_SESSION['client_logged_in'] = true;
            $_SESSION['client_id']        = $user->id;
            $_SESSION['client_username']  = $user->username;
            $_SESSION['client_hoten']     = $user->hoten;
            $_SESSION['client_role']      = $user->role;
            $_SESSION['client_avatar']    = $user->avatar;
            $this->json('success', ['redirect' => URLROOT]);
        }

        $this->json('error', 'Tài khoản hoặc mật khẩu không đúng');
    }

    public function register()
    {
        $data     = json_decode(file_get_contents('php://input'), true) ?? [];
        $hoten    = trim($data['hoten'] ?? '');
        $username = trim($data['username'] ?? '');
        $email    = trim($data['email'] ?? '');
        $password = trim($data['password'] ?? '');

        if (empty($hoten) || empty($username) || empty($password)) {
            $this->json('error', 'Vui lòng điền đủ thông tin');
        }

        $authModel = $this->model('AuthModel');
        if ($authModel->existsByUsernameOrEmail($username, $email)) {
            $this->json('error', 'Tài khoản hoặc email đã tồn tại');
        }

        $authModel->register($hoten, $username, $email, $password);
        $this->json('success', 'Đăng ký thành công, vui lòng đăng nhập!');
    }

    public function logout()
    {
        foreach ($_SESSION as $key => $value) {
            if (strpos($key, 'client_') === 0) {
                unset($_SESSION[$key]);
            }
        }
        $this->json('success', ['redirect' => URLROOT]);
    }
}

