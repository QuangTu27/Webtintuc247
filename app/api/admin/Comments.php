<?php

class Comments extends ApiController
{
    public function __construct() {
        $this->requireAdmin();
        $this->checkPermission();
    }

    private function checkPermission()
    {
        $role = $_SESSION['admin_role'] ?? '';
        $allowedRoles = ['admin', 'editor'];
        if (!in_array($role, $allowedRoles)) {
            $this->json('error', 'Bạn không có quyền quản lý Bình luận');
        }
    }

    // GET /api/comments
    public function index()
    {
        $commentModel = $this->model('CommentModel');

        $q = isset($_GET['q']) ? trim($_GET['q']) : '';
        $catId = isset($_GET['cat_id']) ? (int)$_GET['cat_id'] : 0;
        
        $userRole = $_SESSION['admin_role'] ?? '';
        $userId = (int)($_SESSION['admin_id'] ?? 0);
        $isEditor = ($userRole === 'phongvien' || $userRole === 'nhabao' || $userRole === 'ctv');

        $managerId = $isEditor ? $userId : 0;
        
        $newsList = $commentModel->getNewsListWithCommentCount($managerId, $catId, $q, $isEditor);
        $categories = $commentModel->getParentCategories();

        $this->json('success', [
            'news' => $newsList,
            'categories' => $categories,
            'isEditor' => $isEditor
        ]);
    }

    // GET /api/comments/{id} 
    public function show($id = 0)
    {
        $commentModel = $this->model('CommentModel');
        $newsId = (int)$id;

        $titleRow = $commentModel->getNewsTitleById($newsId);
        
        if (!$titleRow) {
            $this->json('error', 'Bài viết không tồn tại.');
        }

        $comments = $commentModel->getByNewsId($newsId);

        $this->json('success', [
            'news_title' => $titleRow->tieude,
            'comments' => $comments
        ]);
    }

    // DELETE /api/comments/{id}
    public function destroy($id = 0)
    {
        $userRole = $_SESSION['admin_role'] ?? '';
        if ($userRole === 'ctv') {
            $this->json('error', 'Cộng tác viên không có quyền xoá bình luận');
        }

        if ($id > 0) {
            $commentModel = $this->model('CommentModel');
            if ($commentModel->deleteById((int)$id)) {
                $this->json('success', 'Đã xoá bình luận');
            } else {
                $this->json('error', 'Lỗi hệ thống khi xoá');
            }
        }

        $this->json('error', 'Không tìm thấy ID');
    }
}

