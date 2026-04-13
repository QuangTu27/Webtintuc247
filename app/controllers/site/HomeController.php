<?php

class HomeController extends Controller
{
    public function index()
    {
        $CategoriesModel = $this->model('CategoriesModel');
        $userModel = $this->model('UserModel');
        
        $menuItems = $CategoriesModel->getAll();
        
        $avatar = 'default_avatar.png';
        $displayName = 'Người dùng';
        $username = 'Guest';
        
        if (isset($_SESSION['client_logged_in']) && $_SESSION['client_logged_in'] === true && isset($_SESSION['client_id'])) {
            $user = $userModel->getById($_SESSION['client_id']);
            if ($user && is_object($user)) {
                $avatar = !empty($user->avatar) ? $user->avatar : 'default_avatar.png';
                $displayName = !empty($user->hoten) ? $user->hoten : $user->username;
                $username = !empty($user->username) ? $user->username : '';
            }
        }
        
        $data = [
            'menuItems' => $menuItems,
            'avatar' => $avatar,
            'displayName' => $displayName,
            'username' => $username,
        ];
        
        
        $this->view('site/layouts/header', $data);
        $this->view('site/home');
        $this->view('site/layouts/footer');
    }

    public function data()
    {
        header('Content-Type: application/json');
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;
        
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        $siteModel = $this->model('SiteModel');
        
        $allAds = $siteModel->getActiveAds();
        $ads = [];
        if (is_array($allAds)) {
            foreach ($allAds as $ad) {
                $ads[$ad->position][] = $ad;
            }
        }
        
        $news = $siteModel->getPublishedNews($limit, $offset);
        $totalNews = $siteModel->countPublishedNews();
        $totalPages = $totalNews > 0 ? ceil($totalNews / $limit) : 1;
        
        $topViews = $siteModel->getTopViewedNews(5);
        
        echo json_encode([
            'status' => 'success',
            'data' => [
                'ads' => $ads,
                'news' => $news,
                'top_views' => $topViews,
                'pagination' => [
                    'page' => $page,
                    'total_pages' => $totalPages
                ]
            ]
        ]);
        exit;
    }
}

