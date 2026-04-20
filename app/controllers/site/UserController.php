<?php

class UserController extends Controller
{
    private function requireLogin(): void
    {
        if (!isset($_SESSION['client_logged_in']) || $_SESSION['client_logged_in'] !== true) {
            if ($this->isApiRequest()) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Vui lòng đăng nhập']);
                exit;
            }
            header('Location: ' . URLROOT);
            exit;
        }
    }

    private function isApiRequest(): bool
    {
        return $_SERVER['REQUEST_METHOD'] !== 'GET' ||
               isset($_SERVER['HTTP_X_REQUESTED_WITH']) ||
               (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
    }

    private function getUserId(): int
    {
        return (int)($_SESSION['client_id'] ?? 0);
    }

    private function getMenuItems(): array
    {
        return $this->model('CategoriesModel')->getAll();
    }

    public function profile($tab = 'general')
    {
        $this->requireLogin();

        $validTabs = ['general', 'comments', 'bookmarks', 'history'];
        if (!in_array($tab, $validTabs)) {
            $tab = 'general';
        }

        $userId = $this->getUserId();
        $userModel = $this->model('UserModel');
        $user = $userModel->getById($userId);

        $data = [
            'menuItems'   => $this->getMenuItems(),
            'avatar'      => !empty($user->avatar) ? $user->avatar : 'default_avatar.svg',
            'displayName' => !empty($user->hoten) ? $user->hoten : ($user->username ?? 'Người dùng'),
            'username'    => $user->username ?? '',
            'activeTab'   => $tab
        ];

        $this->view('site/layouts/header', $data);
        $this->view('site/users/profile', $data);
        $this->view('site/layouts/footer');
    }


}
