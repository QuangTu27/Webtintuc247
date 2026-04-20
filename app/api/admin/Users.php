<?php

class Users extends ApiController
{
    public function __construct()
    {
        $this->requireAdmin();
    }

    // GET /api/users
    public function index()
    {
        $userModel = $this->model('UserModel');
        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
        $users = $userModel->getAll($keyword);
        $this->json('success', [
            'users' => $users,
            'auth' => ['userId' => $_SESSION['admin_id'] ?? 0]
        ]);
    }

    // [GET] /api/users/{id}
    public function show($id = 0)
    {
        $userModel = $this->model('UserModel');
        $user = $userModel->getById((int)$id);

        if (!$user) {
            $this->json('error', 'Không tìm thấy người dùng');
        }
        $this->json('success', $user);
    }

    // [POST] /api/users
    public function store()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $this->json('error', 'Dữ liệu không hợp lệ');
        }

        $username = trim($input['username'] ?? '');
        $password = trim($input['password'] ?? '');
        $hoten = trim($input['hoten'] ?? '');
        $email = trim($input['email'] ?? '');
        $role = trim($input['role'] ?? 'user');

        if (empty($username) || empty($password) || empty($hoten)) {
            $this->json('error', 'Vui lòng điền đầy đủ User, Pass và Họ Tên');
        }

        $userModel = $this->model('UserModel');

        if ($userModel->existsByUsername($username)) {
            $this->json('error', 'Username đã tồn tại');
        }

        $userModel->create($username, $password, $hoten, $email, $role);
        $this->json('success', 'Thêm người dùng thành công');
    }

    // [PUT] /api/users/{id}
    public function update($id = 0)
    {
        if ($id <= 0) $this->json('error', 'ID không hợp lệ');

        $input = json_decode(file_get_contents('php://input'), true);
        $hoten = trim($input['hoten'] ?? '');
        $email = trim($input['email'] ?? '');
        $role = trim($input['role'] ?? 'user');
        $password = trim($input['password'] ?? '');

        if (empty($hoten)) {
            $this->json('error', 'Họ tên không được để trống');
        }

        $userModel = $this->model('UserModel');
        if (!$userModel->getById((int)$id)) {
            $this->json('error', 'Người dùng không tồn tại');
        }

        $passToUpdate = !empty($password) ? $password : null;
        $userModel->update((int)$id, $hoten, $email, $role, $passToUpdate);

        $this->json('success', 'Cập nhật thành công');
    }

    // DELETE /api/users/1
    public function destroy($id = 0)
    {
        $userModel = $this->model('UserModel');
        $currentAdminId = (int)($_SESSION['admin_id'] ?? 0);

        if ($id > 0) {
            if ($id === $currentAdminId) {
                $this->json('error', 'Không thể tự xóa tài khoản của chính mình');
            }
            $userModel->deleteById((int)$id);
            $this->json('success', 'Đã xoá người dùng');
        }

        // Xóa nhiều
        $input = json_decode(file_get_contents('php://input'), true);
        $ids = array_map('intval', $input['ids'] ?? []);

        if (empty($ids)) {
            $this->json('error', 'Không có ID hợp lệ');
        }

        if (in_array($currentAdminId, $ids)) {
            $this->json('error', 'Không thể tự xóa tài khoản của chính mình');
        }

        $userModel->deleteByIds($ids);
        $this->json('success', 'Đã xoá ' . count($ids) . ' người dùng');
    }
}

