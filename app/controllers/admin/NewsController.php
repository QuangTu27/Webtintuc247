<?php

class NewsController extends Controller
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
        $this->view('admin/news/list');
        $this->view('admin/layouts/footer');
    }

    public function add()
    {
        $this->view('admin/layouts/header');
        $this->view('admin/news/add');
        $this->view('admin/layouts/footer');
    }

    public function edit($id = 0)
    {
        $data = ['id' => (int)$id];
        $this->view('admin/layouts/header');
        $this->view('admin/news/edit', $data);
        $this->view('admin/layouts/footer');
    }

    private function getAuthInfo()
    {
        $role = $_SESSION['admin_role'] ?? '';
        $allowed = ['admin', 'tongbien_tap', 'bien_tap', 'editor'];
        $isAdminOrEditor = in_array($role, $allowed);
        $canPublish = in_array($role, $allowed);
        return [
            'isAdminOrEditor' => $isAdminOrEditor,
            'canPublish' => $canPublish,
            'userId' => (int)($_SESSION['admin_id'] ?? 0)
        ];
    }

}
