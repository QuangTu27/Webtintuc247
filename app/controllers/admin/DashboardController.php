<?php

class DashboardController extends Controller
{
    public function index()
    {
        $this->view('admin/layouts/header');
        $this->view('admin/dashboard');
        $this->view('admin/layouts/footer');
    }

    public function data()
    {
        header('Content-Type: application/json');
        
        $dashboardModel = $this->model('DashboardModel');
        
        $counts = [
            'news' => $dashboardModel->countNews(),
            'pending' => $dashboardModel->countPendingNews(),
            'cats' => $dashboardModel->countCategories(),
            'users' => $dashboardModel->countUsers()
        ];
        
        $pendingList = $dashboardModel->getLatestPendingNews(5);
        
        $role = $_SESSION['admin_role'] ?? 'user';
        $name = $_SESSION['admin_hoten'] ?? $_SESSION['admin_username'] ?? 'Admin';
        
        echo json_encode([
            'status' => 'success',
            'data' => [
                'counts' => $counts,
                'pending_list' => $pendingList,
                'role' => $role,
                'name' => $name
            ]
        ]);
        exit;
    }
}
