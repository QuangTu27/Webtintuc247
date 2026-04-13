<?php

class SearchController extends Controller
{
    public function index()
    {
        $CategoriesModel = $this->model('CategoriesModel');
        $userModel = $this->model('UserModel');
        $siteModel = $this->model('SiteModel');
        
        $menuItems = $CategoriesModel->getAll();
        
        $avatar = 'default_avatar.svg';
        $displayName = 'Người dùng';
        $username = 'Guest';
        
        if (isset($_SESSION['client_logged_in']) && $_SESSION['client_logged_in'] === true && isset($_SESSION['client_id'])) {
            $user = $userModel->getById($_SESSION['client_id']);
            if ($user && is_object($user)) {
                $avatar = !empty($user->avatar) ? $user->avatar : 'default_avatar.svg';
                $displayName = !empty($user->hoten) ? $user->hoten : $user->username;
                $username = !empty($user->username) ? $user->username : '';
            }
        }
        
        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;
        
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        $news = [];
        $totalNews = 0;
        $totalPages = 1;

        if ($keyword !== '') {
            $news = $siteModel->searchNews($keyword, $limit, $offset);
            $totalNews = $siteModel->countSearchResults($keyword);
            $totalPages = $totalNews > 0 ? ceil($totalNews / $limit) : 1;
        }
        
        $data = [
            'menuItems' => $menuItems,
            'avatar' => $avatar,
            'displayName' => $displayName,
            'username' => $username,
            'keyword' => $keyword,
            'news' => $news,
            'total' => $totalNews,
            'page' => $page,
            'total_pages' => $totalPages
        ];
        
        $this->view('site/layouts/header', $data);
        $this->view('site/search', $data);
        $this->view('site/layouts/footer');
    }
}

