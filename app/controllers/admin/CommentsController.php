<?php

class CommentsController extends Controller
{
    public function __construct() {
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            header('Location: ' . URLROOT . 'admin/auth/login'); 
            exit;
        }

        $role = $_SESSION['admin_role'] ?? '';
        $allowedRoles = ['admin', 'editor'];
        
        if (!in_array($role, $allowedRoles)) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'DELETE' || isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Bạn không có quyền quản lý Bình luận']);
                exit;
            }
            
            $this->view('admin/layouts/header');
            echo '<div style="padding: 40px; font-size: 16px; text-align: center; color: #dc3545;">Bạn không có quyền truy cập vào mục này</div>';
            $this->view('admin/layouts/footer');
            exit; 
        }
    }

    public function index()
    {
        $this->view('admin/layouts/header');
        $this->view('admin/comments/list');
        $this->view('admin/layouts/footer');
    }

    public function detail($newsId = 0)
    {
        $data = ['newsId' => (int)$newsId];
        $this->view('admin/layouts/header');
        $this->view('admin/comments/detail', $data);
        $this->view('admin/layouts/footer');
    }

    public function data($newsId = null)
    {
        header('Content-Type: application/json');
        
        $commentModel = $this->model('CommentModel');

        // Trường hợp API gọi dữ liệu của 1 bài viết chi tiết
        if ($newsId !== null && is_numeric($newsId)) {
            $newsId = (int)$newsId;
            $titleRow = $commentModel->getNewsTitleById($newsId);
            
            if (!$titleRow) {
                echo json_encode(['status' => 'error', 'message' => 'Bài viết không tồn tại.']);
                exit;
            }

            $comments = $commentModel->getByNewsId($newsId);

            echo json_encode([
                'status' => 'success',
                'data' => [
                    'news_title' => $titleRow->tieude,
                    'comments' => $comments
                ]
            ]);
            exit;
        }

        // Trường hợp API gọi danh sách bài viết có bình luận (list.php)
        $q = isset($_GET['q']) ? trim($_GET['q']) : '';
        $catId = isset($_GET['cat_id']) ? (int)$_GET['cat_id'] : 0;
        
        // RBAC logic (tương tự Dashboard/News)
        $userRole = $_SESSION['admin_role'] ?? '';
        $userId = (int)($_SESSION['admin_id'] ?? 0);
        $isEditor = ($userRole === 'phongvien' || $userRole === 'nhabao' || $userRole === 'ctv');

        $managerId = $isEditor ? $userId : 0;
        
        $newsList = $commentModel->getNewsListWithCommentCount($managerId, $catId, $q, $isEditor);
        $categories = $commentModel->getParentCategories();

        echo json_encode([
            'status' => 'success',
            'data' => [
                'news' => $newsList,
                'categories' => $categories,
                'isEditor' => $isEditor
            ]
        ]);
        exit;
    }

    public function delete($id = 0)
    {
        header('Content-Type: application/json');
        
        // Ở đây giả sử ai cũng xoá được nếu đã vào được giao diện (hoặc bạn có thể bổ sung check quyền)
        $userRole = $_SESSION['admin_role'] ?? '';
        if ($userRole === 'ctv') {
            echo json_encode(['status' => 'error', 'message' => 'Cộng tác viên không có quyền xoá bình luận']);
            exit;
        }

        if ($id > 0) {
            $commentModel = $this->model('CommentModel');
            if ($commentModel->deleteById((int)$id)) {
                echo json_encode(['status' => 'success', 'message' => 'Đã xoá bình luận']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Lỗi hệ thống khi xoá']);
            }
            exit;
        }

        echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy ID']);
        exit;
    }
}
