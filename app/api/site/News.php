<?php
class News extends ApiController
{
    private function getUserId(): int
    {
        return (isset($_SESSION['client_logged_in']) && $_SESSION['client_logged_in'] === true)
            ? (int)($_SESSION['client_id'] ?? 0) : 0;
    }

    // GET /api/site/news/{id}
    public function show($newsId = 0)
    {
        $newsId = (int)$newsId;
        if ($newsId <= 0) {
            $this->json('error', 'ID bài viết không hợp lệ');
        }

        $newsModel = $this->model('NewsDetailModel');
        $news = $newsModel->getDetailById($newsId);
        if (!$news) {
            $this->json('error', 'Bài viết không tồn tại');
        }

        $newsModel->incrementViewCount($newsId);

        $userId   = $this->getUserId();
        $likes    = $newsModel->countLikes($newsId);
        $is_liked = $userId > 0 ? $newsModel->isLikedByUser($userId, $newsId) : false;
        $is_saved = $userId > 0 ? $newsModel->isSavedByUser($userId, $newsId) : false;
        $comments = $newsModel->getApprovedComments($newsId);

        $this->json('success', [
            'news'     => $news,
            'likes'    => $likes,
            'is_liked' => $is_liked,
            'is_saved' => $is_saved,
            'comments' => $comments,
            'session'  => ['logged_in' => $userId > 0, 'user_id' => $userId]
        ]);
    }

    // POST /api/site/news/{id} 
    public function update($newsId = 0)
    {
        $newsId = (int)$newsId;
        if ($newsId <= 0) {
            $this->json('error', 'ID không hợp lệ');
        }

        $userId = $this->getUserId();
        if ($userId <= 0) {
            $this->json('error', 'Vui lòng đăng nhập');
        }

        $input  = json_decode(file_get_contents('php://input'), true) ?? [];
        $action = $input['action'] ?? '';

        if ($action === 'toggle_like') {
            $newsModel = $this->model('NewsDetailModel');
            $result    = $newsModel->toggleLike($userId, $newsId);
            $this->json('success', ['action' => $result]);
        }

        if ($action === 'toggle_save') {
            $saved = $this->model('UserProfileModel')->toggleBookmark($userId, $newsId);
            $this->json('success', ['action' => $saved ? 'saved' : 'unsaved']);
        }

        $this->json('error', 'Hành động không xác định');
    }

    // POST /api/site/news 
    public function store()
    {
        $this->handleCommentAction();
    }

    public function like()
    {
        $userId = $this->getUserId();
        if ($userId <= 0) { $this->json('error', 'Vui lòng đăng nhập'); }

        $input  = json_decode(file_get_contents('php://input'), true) ?? [];
        $newsId = (int)($input['news_id'] ?? 0);
        if ($newsId <= 0) { $this->json('error', 'ID không hợp lệ'); }

        $result = $this->model('NewsDetailModel')->toggleLike($userId, $newsId);
        $this->json('success', ['action' => $result]);
    }

    public function save()
    {
        $userId = $this->getUserId();
        if ($userId <= 0) { $this->json('error', 'Vui lòng đăng nhập'); }

        $input  = json_decode(file_get_contents('php://input'), true) ?? [];
        $newsId = (int)($input['news_id'] ?? 0);
        if ($newsId <= 0) { $this->json('error', 'ID không hợp lệ'); }

        $saved = $this->model('UserProfileModel')->toggleBookmark($userId, $newsId);
        $this->json('success', ['action' => $saved ? 'saved' : 'unsaved']);
    }

    public function comment()
    {
        $this->handleCommentAction();
    }

    private function handleCommentAction()
    {
        $userId = $this->getUserId();
        if ($userId <= 0) {
            $this->json('error', 'Vui lòng đăng nhập');
        }

        $input  = json_decode(file_get_contents('php://input'), true) ?? [];
        $action = $input['action'] ?? 'post_comment';

        $newsModel = $this->model('NewsDetailModel');

        if ($action === 'edit_comment') {
            $commentId = (int)($input['comment_id'] ?? 0);
            $noidung   = trim($input['noidung'] ?? '');
            if (!$commentId || empty($noidung)) { $this->json('error', 'Dữ liệu không hợp lệ'); }
            $newsModel->editComment($commentId, $noidung);
            $this->json('success', 'Sửa bình luận thành công!');
        }

        if ($action === 'delete_comment') {
            $commentId = (int)($input['comment_id'] ?? 0);
            if (!$commentId) { $this->json('error', 'Dữ liệu không hợp lệ'); }
            if (!$newsModel->isCommentOwnedByUser($commentId, $userId)) {
                $this->json('error', 'Bạn không có quyền xoá bình luận này');
            }
            $newsModel->softDeleteComment($commentId);
            $this->json('success', 'Đã xoá bình luận');
        }

        $newsId   = (int)($input['news_id'] ?? 0);
        $noidung  = trim($input['noidung'] ?? '');
        $parentId = isset($input['parent_id']) && $input['parent_id'] > 0 ? (int)$input['parent_id'] : null;

        if ($newsId <= 0 || empty($noidung)) {
            $this->json('error', 'Dữ liệu không hợp lệ');
        }

        $userModel     = $this->model('UserModel');
        $user          = $userModel->getById($userId);
        $tenNguoiBinh  = $user && !empty($user->hoten) ? $user->hoten : ($user->username ?? 'Khách');

        $newsModel->postComment($newsId, $userId, $tenNguoiBinh, htmlspecialchars($noidung), $parentId, 1);
        $this->json('success', 'Gửi bình luận thành công!');
    }
}

