<?php

class CategoriesController extends Controller
{
    private function requireAdmin(): void
    {
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Chưa đăng nhập']);
            exit;
        }
    }

    private function requireAdminPage(): void
    {
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            header('Location: ' . URLROOT . 'admin/auth/login');
            exit;
        }
    }

    private function isAdmin(): bool
    {
        $role = $_SESSION['admin_role'] ?? '';
        return in_array($role, ['admin']);
    }

    private function isEditor(): bool
    {
        $role = $_SESSION['admin_role'] ?? '';
        return in_array($role, ['editor', 'admin']);
    }

    public function index()
    {
        $this->requireAdminPage();
        $this->view('admin/layouts/header');
        $this->view('admin/categories/list');
        $this->view('admin/layouts/footer');
    }

    public function add()
    {
        $this->requireAdminPage();
        $data = ['id' => 0];
        $this->view('admin/layouts/header');
        $this->view('admin/categories/add', $data);
        $this->view('admin/layouts/footer');
    }

    public function edit($id = 0)
    {
        $this->requireAdminPage();
        $data = ['id' => (int)$id];
        $this->view('admin/layouts/header');
        $this->view('admin/categories/edit', $data);
        $this->view('admin/layouts/footer');
    }

}
