<?php

class UsersController extends Controller
{
    public function __construct() {
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            header('Location: ' . URLROOT . 'admin/auth/login'); exit;
        }
        $role = $_SESSION['admin_role'] ?? ''; 

        $url = isset($_GET['url']) ? explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL)) : [];
        $action = $url[2] ?? 'index';

        if ($role !== 'admin' && $action !== 'profile') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'DELETE' || isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Bạn không có quyền truy cập']);
                exit;
            }
            
            $this->view('admin/layouts/header');
            echo '<div style="padding: 40px; font-size: 16px; text-align: center; color: #dc3545;">Bạn không có quyền truy cập vào mục này</div>';
            $this->view('admin/layouts/footer');
            exit; 
        }
    }

    public function index()
    {
        $this->view('admin/layouts/header');
        $this->view('admin/users/list');
        $this->view('admin/layouts/footer');
    }

    public function add()
    {
        $this->view('admin/layouts/header');
        $this->view('admin/users/add');
        $this->view('admin/layouts/footer');
    }

    public function edit($id = 0)
    {
        $data = ['id' => (int)$id];
        $this->view('admin/layouts/header');
        $this->view('admin/users/edit', $data);
        $this->view('admin/layouts/footer');
    }
    
    public function data()
    {
        header('Content-Type: application/json');

        $userModel = $this->model('UserModel');
        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
        
        $users = $userModel->getAll($keyword);

        echo json_encode([
            'status' => 'success',
            'data' => $users,
            'auth' => [
                'userId' => (int)($_SESSION['admin_id'] ?? 0)
            ]
        ]);
        exit;
    }

    public function show($id = 0)
    {
        header('Content-Type: application/json');

        $userModel = $this->model('UserModel');
        $user = $userModel->getById((int)$id);

        if (!$user) {
            echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy người dùng']);
            exit;
        }

        echo json_encode(['status' => 'success', 'data' => $user]);
        exit;
    }

    public function store()
    {
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);
        $username = trim($input['username'] ?? '');
        $password = trim($input['password'] ?? '');
        $hoten = trim($input['hoten'] ?? '');
        $email = trim($input['email'] ?? '');
        $role = trim($input['role'] ?? 'user');

        if (empty($username) || empty($password) || empty($hoten)) {
            echo json_encode(['status' => 'error', 'message' => 'Vui lòng điền đầy đủ User, Pass và Họ Tên']);
            exit;
        }

        $userModel = $this->model('UserModel');

        if ($userModel->existsByUsername($username)) {
            echo json_encode(['status' => 'error', 'message' => 'Username đã tồn tại']);
            exit;
        }

        $userModel->create($username, $password, $hoten, $email, $role);

        echo json_encode(['status' => 'success', 'message' => 'Thêm thành công']);
        exit;
    }

    public function update($id = 0)
    {
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);
        $hoten = trim($input['hoten'] ?? '');
        $email = trim($input['email'] ?? '');
        $role = trim($input['role'] ?? 'user');
        $password = trim($input['password'] ?? '');

        if (empty($hoten)) {
            echo json_encode(['status' => 'error', 'message' => 'Họ tên không được để trống']);
            exit;
        }

        $userModel = $this->model('UserModel');
        $existing = $userModel->getById((int)$id);

        if (!$existing) {
            echo json_encode(['status' => 'error', 'message' => 'Người dùng không tồn tại']);
            exit;
        }

        $passToUpdate = !empty($password) ? $password : null;
        $userModel->update((int)$id, $hoten, $email, $role, $passToUpdate);

        echo json_encode(['status' => 'success', 'message' => 'Cập nhật thành công']);
        exit;
    }

    public function delete($id = 0)
    {
        header('Content-Type: application/json');
        
        $userModel = $this->model('UserModel');
        $currentAdminId = (int)($_SESSION['admin_id'] ?? 0);

        // Xoá 1
        if ($id > 0) {
            if ($id === $currentAdminId) {
                echo json_encode(['status' => 'error', 'message' => 'Không thể tự xóa tài khoản của chính mình']);
                exit;
            }
            $userModel->deleteById((int)$id);
            echo json_encode(['status' => 'success', 'message' => 'Đã xoá người dùng']);
            exit;
        }

        // Xoá nhiều
        $input = json_decode(file_get_contents('php://input'), true);
        $ids = array_map('intval', $input['ids'] ?? []);

        if (empty($ids)) {
            echo json_encode(['status' => 'error', 'message' => 'Không có ID hợp lệ']);
            exit;
        }

        if (in_array($currentAdminId, $ids)) {
            echo json_encode(['status' => 'error', 'message' => 'Phát hiện ID của bạn trong danh sách, không thể tự xóa']);
            exit;
        }

        $userModel->deleteByIds($ids);
        echo json_encode(['status' => 'success', 'message' => 'Đã xoá ' . count($ids) . ' người dùng']);
        exit;
    }
}