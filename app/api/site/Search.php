<?php

class Search extends ApiController
{
    // GET /api/site/search
    public function index()
    {
        $keyword    = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
        $page       = max(1, (int)($_GET['page'] ?? 1));
        $limit      = 10;
        $offset     = ($page - 1) * $limit;

        if ($keyword === '') {
            $this->json('success', [
                'news'       => [],
                'total'      => 0,
                'pagination' => ['page' => 1, 'total_pages' => 1]
            ]);
        }

        $siteModel  = $this->model('SiteModel');
        $news       = $siteModel->searchNews($keyword, $limit, $offset);
        $totalNews  = $siteModel->countSearchResults($keyword);
        $totalPages = $totalNews > 0 ? ceil($totalNews / $limit) : 1;

        $this->json('success', [
            'news'       => $news,
            'total'      => $totalNews,
            'pagination' => ['page' => $page, 'total_pages' => $totalPages]
        ]);
    }
}

