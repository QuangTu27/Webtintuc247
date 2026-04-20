<?php

class Dashboard extends ApiController
{
    public function __construct() {
        $this->requireAdmin();
    }

    public function index()
    {
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
        
        $this->json('success', [
            'counts' => $counts,
            'pending_list' => $pendingList,
            'role' => $role,
            'name' => $name
        ]);
    }
}

