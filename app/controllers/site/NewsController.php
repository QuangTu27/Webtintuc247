<?php

class NewsController extends Controller
{
    public function index($newsId = 0)
    {
        if (!$newsId) {
            header('Location: ' . URLROOT);
            exit;
        }

        $CategoriesModel = $this->model('CategoriesModel');
        $userModel = $this->model('UserModel');
        
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
        
        $data = [
            'menuItems' => $menuItems,
            'avatar' => $avatar,
            'displayName' => $displayName,
            'username' => $username,
            'newsId' => (int)$newsId
        ];
        
        $this->view('site/layouts/header', $data);
        $this->view('site/news_detail', $data);
        $this->view('site/layouts/footer');
    }

    public function detail($newsId = 0)
    {
        header('Content-Type: application/json');
        
        if (!$newsId) {
            echo json_encode(['status' => 'error', 'message' => 'Lỗi ID']);
            exit;
        }

        $newsModel = $this->model('NewsDetailModel');
        
        $news = $newsModel->getDetailById($newsId);
        if (!$news) {
            echo json_encode(['status' => 'error', 'message' => 'Bài viết không tồn tại']);
            exit;
        }

        $newsModel->incrementViewCount($newsId);
        
        $userId = isset($_SESSION['client_logged_in']) && $_SESSION['client_logged_in'] === true ? (int)$_SESSION['client_id'] : 0;
        
        $likes = $newsModel->countLikes($newsId);
        $is_liked = $userId > 0 ? $newsModel->isLikedByUser($userId, $newsId) : false;
        $is_saved = $userId > 0 ? $newsModel->isSavedByUser($userId, $newsId) : false;
        $comments = $newsModel->getApprovedComments($newsId);
        
        echo json_encode([
            'status' => 'success',
            'data' => [
                'news' => $news,
                'likes' => $likes,
                'is_liked' => $is_liked,
                'is_saved' => $is_saved,
                'comments' => $comments,
                'session' => [
                    'logged_in' => $userId > 0,
                    'user_id' => $userId
                ]
            ]
        ]);
        exit;
    }

    public function like()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Invalid method']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $newsId = isset($data['news_id']) ? (int)$data['news_id'] : 0;
        
        if (!isset($_SESSION['client_logged_in']) || $_SESSION['client_logged_in'] !== true || $newsId <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Vui lòng đăng nhập']);
            exit;
        }

        $newsModel = $this->model('NewsDetailModel');
        $action = $newsModel->toggleLike($_SESSION['client_id'], $newsId);
        
        echo json_encode([
            'status' => 'success',
            'action' => $action
        ]);
        exit;
    }

    public function save()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Invalid method']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $newsId = isset($data['news_id']) ? (int)$data['news_id'] : 0;
        
        if (!isset($_SESSION['client_logged_in']) || $_SESSION['client_logged_in'] !== true || $newsId <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Vui lòng đăng nhập']);
            exit;
        }

        $newsModel = $this->model('NewsDetailModel');
        $action = $newsModel->toggleSave($_SESSION['client_id'], $newsId);
        
        echo json_encode([
            'status' => 'success',
            'action' => $action
        ]);
        exit;
    }

    public function comment($action = null)
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Invalid method']);
            exit;
        }

        if (!isset($_SESSION['client_logged_in']) || $_SESSION['client_logged_in'] !== true) {
            echo json_encode(['status' => 'error', 'message' => 'Vui lòng đăng nhập']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $newsModel = $this->model('NewsDetailModel');
        $userId = (int)$_SESSION['client_id'];
        
        if ($action === 'edit') {
            $commentId = isset($data['comment_id']) ? (int)$data['comment_id'] : 0;
            $noidung = isset($data['noidung']) ? trim($data['noidung']) : '';
            
            if (!$commentId || empty($noidung)) {
                echo json_encode(['status' => 'error', 'message' => 'Dữ liệu không hợp lệ']);
                exit;
            }
            
            $newsModel->editComment($commentId, $noidung);
            echo json_encode(['status' => 'success', 'message' => 'Sửa bình luận thành công!']);
            exit;
        } 
        else if ($action === 'delete') {
            $commentId = isset($data['comment_id']) ? (int)$data['comment_id'] : 0;
            
            if (!$commentId) {
                echo json_encode(['status' => 'error', 'message' => 'Dữ liệu không hợp lệ']);
                exit;
            }
            
            if (!$newsModel->isCommentOwnedByUser($commentId, $userId)) {
                echo json_encode(['status' => 'error', 'message' => 'Bạn không có quyền xoá bình luận này']);
                exit;
            }
            
            $newsModel->softDeleteComment($commentId);
            echo json_encode(['status' => 'success', 'message' => 'Đã xoá bình luận']);
            exit;
        }
        else {
            $newsId = isset($data['news_id']) ? (int)$data['news_id'] : 0;
            $noidung = isset($data['noidung']) ? trim($data['noidung']) : '';
            $parentId = isset($data['parent_id']) && $data['parent_id'] > 0 ? (int)$data['parent_id'] : null;
            
            if ($newsId <= 0 || empty($noidung)) {
                echo json_encode(['status' => 'error', 'message' => 'Dữ liệu không hợp lệ']);
                exit;
            }
            
            $userModel = $this->model('UserModel');
            $user = $userModel->getById($userId);
            $tenNguoiBinh = $user && !empty($user->hoten) ? $user->hoten : ($user->username ?? 'Khách');
            
            $newsModel->postComment($newsId, $userId, $tenNguoiBinh, htmlspecialchars($noidung), $parentId, 1);
            echo json_encode(['status' => 'success', 'message' => 'Gửi bình luận thành công!']);
            exit;
        }
    }
}

