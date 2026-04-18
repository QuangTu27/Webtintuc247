<?php

class HomeController extends Controller
{
    public function index()
    {
        $data = $this->getClientViewData();

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

