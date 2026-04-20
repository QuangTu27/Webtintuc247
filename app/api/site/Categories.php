<?php

class Categories extends ApiController
{
    // GET /api/site/categories/{id}
    public function show($catId = 0)
    {
        $catId = (int)$catId;
        if ($catId <= 0) {
            $this->json('error', 'Danh mục không hợp lệ');
        }

        $page   = max(1, (int)($_GET['page'] ?? 1));
        $limit  = 10;
        $offset = ($page - 1) * $limit;

        $siteModel = $this->model('SiteModel');

        $parentInfo = $siteModel->getCategoryById($catId);
        if (!$parentInfo) {
            $this->json('error', 'Không tìm thấy danh mục');
        }

        $categoryIds   = [$catId];
        $subCategories = [];

        if ($parentInfo->parent_id == 0) {
            $subCategories = $siteModel->getSubCategories($catId);
            foreach ($subCategories as $sub) {
                $categoryIds[] = $sub->id;
            }
        } else {
            $parentInfo    = $siteModel->getCategoryById($parentInfo->parent_id);
            if ($parentInfo) {
                $subCategories = $siteModel->getSubCategories($parentInfo->id);
            }
        }

        $news       = $siteModel->getNewsByCategories($categoryIds, $limit, $offset);
        $totalNews  = $siteModel->countNewsByCategories($categoryIds);
        $totalPages = $totalNews > 0 ? ceil($totalNews / $limit) : 1;
        $topViews   = $siteModel->getTopViewedNewsByCategories($categoryIds, 5);

        $this->json('success', [
            'parent_info'    => $parentInfo,
            'sub_categories' => $subCategories,
            'news'           => $news,
            'top_views'      => $topViews,
            'pagination'     => [
                'page'          => $page,
                'total_pages'   => $totalPages,
                'total_results' => $totalNews
            ]
        ]);
    }
}

