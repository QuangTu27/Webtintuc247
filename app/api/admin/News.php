<?php

class News extends ApiController
{
    public function __construct() {
        $this->requireAdmin();
    }

    private function getAuthInfo()
    {
        $role = $_SESSION['admin_role'] ?? '';
        $allowed = ['admin', 'tongbien_tap', 'bien_tap', 'editor'];
        $isAdminOrEditor = in_array($role, $allowed);
        $canPublish = in_array($role, $allowed);
        return [
            'isAdminOrEditor' => $isAdminOrEditor,
            'canPublish' => $canPublish,
            'userId' => (int)($_SESSION['admin_id'] ?? 0)
        ];
    }

    // GET /api/news/formdata
    public function formdata()
    {
        $newsModel = $this->model('NewsModel');
        $categories = $newsModel->getCategoriesWithParent();
        
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'data' => ['categories' => $categories],
            'auth' => $this->getAuthInfo()
        ]);
        exit;
    }

    // GET /api/news 
    public function index()
    {
        $catId = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
        $newsModel = $this->model('NewsModel');
        $news = $newsModel->getAll($catId);

        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'data' => $news,
            'auth' => $this->getAuthInfo()
        ]);
        exit;
    }

    // GET /api/news/{id} 
    public function show($id = 0)
    {
        $newsModel = $this->model('NewsModel');
        $post = $newsModel->getById((int)$id);

        if (!$post) {
            $this->json('error', 'Bài viết không tồn tại.');
        }

        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'data' => $post]);
        exit;
    }

    // POST /api/news 
    public function store()
    {
        $tieude = trim($_POST['tieude'] ?? '');
        $danhmuc = (int)($_POST['danhmuc'] ?? 0);
        $tomtat = trim($_POST['tomtat'] ?? '');
        $noidung = trim($_POST['noidung'] ?? '');
        $trangthai = trim($_POST['trangthai'] ?? 'cho_duyet');
        $authorId = (int)($_SESSION['admin_id'] ?? 0);

        if (empty($tieude) || $danhmuc <= 0 || empty($noidung)) {
            $this->json('error', 'Vui lòng nhập đầy đủ Tiêu đề, Danh mục và Nội dung.');
        }

        $hinhanh = '';
        if (isset($_FILES['hinhanh']) && $_FILES['hinhanh']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['hinhanh']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (!in_array($ext, $allowed)) {
                $this->json('error', 'Định dạng ảnh không hợp lệ');
            }
            $hinhanh = time() . '_' . rand(100, 999) . '.' . $ext;
            $uploadDir = dirname(APPROOT) . '/assets/images/news/';
            move_uploaded_file($_FILES['hinhanh']['tmp_name'], $uploadDir . $hinhanh);
        } else {
            $this->json('error', 'Vui lòng chọn ảnh đại diện.');
        }

        $newsModel = $this->model('NewsModel');
        $newsModel->create($tieude, $tomtat, $noidung, $danhmuc, $trangthai, $hinhanh, $authorId);
        
        $this->json('success', 'Thêm thành công');
    }

    // POST /api/news/{id} 
    public function update($id = 0)
    {
        if ($id <= 0) {
            $this->json('error', 'ID không hợp lệ');
        }

        $tieude = trim($_POST['tieude'] ?? '');
        $danhmuc = (int)($_POST['danhmuc'] ?? 0);
        $tomtat = trim($_POST['tomtat'] ?? '');
        $noidung = trim($_POST['noidung'] ?? '');
        $trangthai = trim($_POST['trangthai'] ?? 'cho_duyet');

        if (empty($tieude) || $danhmuc <= 0 || empty($noidung)) {
            $this->json('error', 'Vui lòng nhập đủ các trường yêu cầu.');
        }

        $newsModel = $this->model('NewsModel');
        $post = $newsModel->getById($id);
        if (!$post) {
            $this->json('error', 'Không tìm bảng tin.');
        }

        $hinhanh = $post->hinhanh;
        if (isset($_FILES['hinhanh']) && $_FILES['hinhanh']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['hinhanh']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (!in_array($ext, $allowed)) {
                $this->json('error', 'Ảnh thay thế không hợp lệ');
            }
            $hinhanh = time() . '_' . rand(100, 999) . '.' . $ext;
            $uploadDir = dirname(APPROOT) . '/assets/images/news/';
            move_uploaded_file($_FILES['hinhanh']['tmp_name'], $uploadDir . $hinhanh);

            if (!empty($post->hinhanh) && file_exists($uploadDir . $post->hinhanh)) {
                unlink($uploadDir . $post->hinhanh);
            }
        }

        $newsModel->update($id, $tieude, $tomtat, $noidung, $danhmuc, $trangthai, $hinhanh);
        
        $this->json('success', 'Cập nhật thành công');
    }

    // POST /api/news/status
    public function status()
    {
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $id = (int)($input['id'] ?? 0);
        $action = $input['status_action'] ?? '';

        if ($id <= 0) {
            $this->json('error', 'Thiếu ID');
        }

        $newStatus = '';
        if ($action === 'approve') $newStatus = 'da_dang';
        else if ($action === 'hide') $newStatus = 'ban_nhap';

        if (!$newStatus) {
            $this->json('error', 'Hành động không xác định');
        }

        $newsModel = $this->model('NewsModel');
        $newsModel->updateStatus($id, $newStatus);

        $this->json('success', 'Đã cập nhật trạng thái');
    }

    // DELETE /api/news/{id}
    public function destroy($id = 0)
    {
        $newsModel = $this->model('NewsModel');
        $uploadDir = dirname(APPROOT) . '/assets/images/news/';

        if ($id > 0) {
            $post = $newsModel->getAuthorAndImage((int)$id);
            if ($post) {
                if (!empty($post->hinhanh) && file_exists($uploadDir . $post->hinhanh)) {
                    unlink($uploadDir . $post->hinhanh);
                }
                $newsModel->deleteById((int)$id);
            }
            $this->json('success', 'Đã xóa bài');
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $ids = array_map('intval', $input['ids'] ?? []);

        if (empty($ids)) {
            $this->json('error', 'Không có ID hợp lệ');
        }

        $images = $newsModel->getImagesByIds($ids);
        foreach ($images as $imgRow) {
            if (!empty($imgRow->hinhanh) && file_exists($uploadDir . $imgRow->hinhanh)) {
                unlink($uploadDir . $imgRow->hinhanh);
            }
        }

        $newsModel->deleteByIds($ids);
        $this->json('success', 'Đã xóa ' . count($ids) . ' bài');
    }
}

