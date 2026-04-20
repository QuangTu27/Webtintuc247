<?php

class AdsController extends Controller
{
    public function __construct() {
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            header('Location: ' . URLROOT . 'admin/auth/login'); 
            exit;
        }
        
        $role = $_SESSION['admin_role'] ?? '';
        
        if ($role !== 'admin') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'DELETE' || isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Bạn không có quyền quản lý Quảng cáo']);
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
        $this->view('admin/ads/list');
        $this->view('admin/layouts/footer');
    }

    public function add()
    {
        $this->view('admin/layouts/header');
        $this->view('admin/ads/add');
        $this->view('admin/layouts/footer');
    }

    public function edit($id = 0)
    {
        $data = ['id' => (int)$id];
        $this->view('admin/layouts/header');
        $this->view('admin/ads/edit', $data);
        $this->view('admin/layouts/footer');
    }

}
