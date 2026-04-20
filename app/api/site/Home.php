<?php

class Home extends ApiController
{
    // GET /api/site/home
    public function index()
    {
        $page = max(1, (int)($_GET['page'] ?? 1));
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

        $news       = $siteModel->getPublishedNews($limit, $offset);
        $totalNews  = $siteModel->countPublishedNews();
        $totalPages = $totalNews > 0 ? ceil($totalNews / $limit) : 1;
        $topViews   = $siteModel->getTopViewedNews(5);

        $this->json('success', [
            'ads'        => $ads,
            'news'       => $news,
            'top_views'  => $topViews,
            'pagination' => ['page' => $page, 'total_pages' => $totalPages]
        ]);
    }
}

