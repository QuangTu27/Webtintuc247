<?php

class SearchController extends Controller
{
    public function index()
    {
        $data = $this->getClientViewData();
        $this->view('site/layouts/header', $data);
        $this->view('site/search', $data);
        $this->view('site/layouts/footer');
    }

    /** GET /search/data?keyword=...&page=... */
    public function data()
    {
        header('Content-Type: application/json');

        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $limit   = 10;
        $offset  = ($page - 1) * $limit;

        if ($keyword === '') {
            echo json_encode(['status' => 'success', 'data' => [
                'news' => [], 'total' => 0,
                'pagination' => ['page' => 1, 'total_pages' => 1]
            ]]);
            exit;
        }

        $siteModel  = $this->model('SiteModel');
        $news       = $siteModel->searchNews($keyword, $limit, $offset);
        $totalNews  = $siteModel->countSearchResults($keyword);
        $totalPages = $totalNews > 0 ? ceil($totalNews / $limit) : 1;

        echo json_encode([
            'status' => 'success',
            'data'   => [
                'news'       => $news,
                'total'      => $totalNews,
                'pagination' => [
                    'page'        => $page,
                    'total_pages' => $totalPages,
                ]
            ]
        ]);
        exit;
    }
}
