<?php

class CommentsController extends Controller
{
    public function __construct() {
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            header('Location: ' . URLROOT . 'admin/auth/login'); 
            exit;
        }

        $role = $_SESSION['admin_role'] ?? '';
        $allowedRoles = ['admin', 'editor'];
        
        if (!in_array($role, $allowedRoles)) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'DELETE' || isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Bạn không có quyền quản lý Bình luận']);
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
        $this->view('admin/comments/list');
        $this->view('admin/layouts/footer');
    }

    public function detail($newsId = 0)
    {
        $data = ['newsId' => (int)$newsId];
        $this->view('admin/layouts/header');
        $this->view('admin/comments/detail', $data);
        $this->view('admin/layouts/footer');
    }

}
