<?php

class CategoriesController extends Controller
{
    public function index($catId = 0, $action = null)
    {
        if ($action === 'data') {
            return $this->data($catId);
        }

        $data = $this->getClientViewData();
        $data['catId'] = (int)$catId;

        $this->view('site/layouts/header', $data);
        $this->view('site/categories', $data);
        $this->view('site/layouts/footer');
    }

    private function data($catId)
    {
        header('Content-Type: application/json');
        if (!$catId) {
            echo json_encode(['status' => 'error', 'message' => 'Danh mục không hợp lệ']);
            exit;
        }

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;
        
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        $siteModel = $this->model('SiteModel');
        $CategoriesModel = $this->model('CategoriesModel');
        
        $parentInfo = $siteModel->getCategoryById($catId);
        if (!$parentInfo) {
            echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy danh mục']);
            exit;
        }
        
        $categoryIds = [$catId];
        $subCategories = [];

        if ($parentInfo->parent_id == 0) {
            $subCategories = $siteModel->getSubCategories($catId);
            foreach ($subCategories as $sub) {
                $categoryIds[] = $sub->id;
            }
        } else {
            $parentInfo = $siteModel->getCategoryById($parentInfo->parent_id);
            if ($parentInfo) {
                $subCategories = $siteModel->getSubCategories($parentInfo->id);
            }
        }

        $news = $siteModel->getNewsByCategories($categoryIds, $limit, $offset);
        $totalNews = $siteModel->countNewsByCategories($categoryIds);
        $totalPages = $totalNews > 0 ? ceil($totalNews / $limit) : 1;
        
        $topViews = $siteModel->getTopViewedNewsByCategories($categoryIds, 5);
        
        echo json_encode([
            'status' => 'success',
            'data' => [
                'parent_info' => $parentInfo,
                'sub_categories' => $subCategories,
                'news' => $news,
                'top_views' => $topViews,
                'pagination' => [
                    'page' => $page,
                    'total_pages' => $totalPages,
                    'total_results' => $totalNews
                ]
            ]
        ]);
        exit;
    }
}

