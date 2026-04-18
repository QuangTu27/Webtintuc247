<?php

class AuthController extends Controller
{
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (isset($_SESSION['client_logged_in']) && $_SESSION['client_logged_in'] === true) {
                header('Location: ' . URLROOT);
                exit;
            }
            $this->view('site/auth/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
            $data = json_decode(file_get_contents('php://input'), true);
            $username = trim($data['username'] ?? '');
            $password = trim($data['password'] ?? '');

            if (empty($username) || empty($password)) {
                echo json_encode(['status' => 'error', 'message' => 'Vui lòng nhập đủ thông tin']);
                exit;
            }

            $authModel = $this->model('AuthModel');
            
            $admin = $authModel->findAdminByCredentials($username, $password);
            if ($admin) {
                echo json_encode(['status' => 'error', 'message' => 'Tài khoản admin vui lòng đăng nhập qua trang quản trị']);
                exit;
            }

            $user = $authModel->findUserByCredentials($username, $password);
            if ($user) {
                $_SESSION['client_logged_in'] = true;
                $_SESSION['client_id'] = $user->id;
                $_SESSION['client_username'] = $user->username;
                $_SESSION['client_hoten'] = $user->hoten;
                $_SESSION['client_role'] = $user->role;
                $_SESSION['client_avatar'] = $user->avatar;

                echo json_encode(['status' => 'success', 'redirect' => URLROOT]);
                exit;
            }

            echo json_encode(['status' => 'error', 'message' => 'Tài khoản hoặc mật khẩu không đúng']);
            exit;
        }

        echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
        exit;
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
            $data = json_decode(file_get_contents('php://input'), true);
            
            $hoten = trim($data['hoten'] ?? '');
            $username = trim($data['username'] ?? '');
            $email = trim($data['email'] ?? '');
            $password = trim($data['password'] ?? '');

            if (empty($hoten) || empty($username) || empty($password)) {
                echo json_encode(['status' => 'error', 'message' => 'Vui lòng điền đủ thông tin']);
                exit;
            }

            $authModel = $this->model('AuthModel');
            
            if ($authModel->existsByUsernameOrEmail($username, $email)) {
                echo json_encode(['status' => 'error', 'message' => 'Tài khoản hoặc email đã tồn tại']);
                exit;
            }

            $authModel->register($hoten, $username, $email, $password);
            
            echo json_encode(['status' => 'success', 'message' => 'Đăng ký thành công, vui lòng đăng nhập!']);
            exit;
        }
        
        header('Location: ' . URLROOT);
        exit;
    }

    public function logout()
    {
        foreach ($_SESSION as $key => $value) {
            if (strpos($key, 'client_') === 0) {
                unset($_SESSION[$key]);
            }
        }
        header('Location: ' . URLROOT);
        exit;
    }
}
