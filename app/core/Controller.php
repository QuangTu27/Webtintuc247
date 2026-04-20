<?php

class Controller
{
    public function model(string $model): object
    {
        require_once APPROOT . '/models/' . $model . '.php';
        return new $model();
    }

    public function view(string $view, array $data = []): void
    {
        if (file_exists(APPROOT . '/views/' . $view . '.php')) {
            extract($data);
            require_once APPROOT . '/views/' . $view . '.php';
        } else {
            die('View ' . $view . ' does not exist.');
        }
    }


    protected function getClientViewData(): array
    {
        $menuItems = $this->model('CategoriesModel')->getAll();

        $avatar      = 'default_avatar.png';
        $displayName = 'Người dùng';
        $username    = 'Guest';

        if (isset($_SESSION['client_logged_in']) && $_SESSION['client_logged_in'] === true && isset($_SESSION['client_id'])) {
            $user = $this->model('UserModel')->getById((int)$_SESSION['client_id']);
            if ($user && is_object($user)) {
                $avatar      = !empty($user->avatar)   ? $user->avatar   : 'default_avatar.png';
                $displayName = !empty($user->hoten)    ? $user->hoten    : ($user->username ?? 'Người dùng');
                $username    = !empty($user->username) ? $user->username : '';
            }
        }

        return compact('menuItems', 'avatar', 'displayName', 'username');
    }
}
