<?php

class NewsController extends Controller
{
    public function __construct() {
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            header('Location: ' . URLROOT . 'admin/auth/login'); 
            exit;
        }
    }

    public function index()
    {
        $this->view('admin/layouts/header');
        $this->view('admin/news/list');
        $this->view('admin/layouts/footer');
    }

    public function add()
    {
        $this->view('admin/layouts/header');
        $this->view('admin/news/add');
        $this->view('admin/layouts/footer');
    }

    public function edit($id = 0)
    {
        $data = ['id' => (int)$id];
        $this->view('admin/layouts/header');
        $this->view('admin/news/edit', $data);
        $this->view('admin/layouts/footer');
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

    public function formdata()
    {
        header('Content-Type: application/json');
        $newsModel = $this->model('NewsModel');
        $categories = $newsModel->getCategoriesWithParent();
        
        echo json_encode([
            'status' => 'success',
            'auth' => $this->getAuthInfo(),
            'data' => [
                'categories' => $categories
            ]
        ]);
        exit;
    }

    public function data()
    {
        header('Content-Type: application/json');
        
        $catId = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
        $newsModel = $this->model('NewsModel');
        $news = $newsModel->getAll($catId);

        echo json_encode([
            'status' => 'success',
            'auth' => $this->getAuthInfo(),
            'data' => $news
        ]);
        exit;
    }

    public function show($id = 0)
    {
        header('Content-Type: application/json');

        $newsModel = $this->model('NewsModel');
        $post = $newsModel->getById((int)$id);

        if (!$post) {
            echo json_encode(['status' => 'error', 'message' => 'Bài viết không tồn tại.']);
            exit;
        }

        echo json_encode(['status' => 'success', 'data' => $post]);
        exit;
    }

    public function store()
    {
        header('Content-Type: application/json');
        
        $tieude = trim($_POST['tieude'] ?? '');
        $danhmuc = (int)($_POST['danhmuc'] ?? 0);
        $tomtat = trim($_POST['tomtat'] ?? '');
        $noidung = trim($_POST['noidung'] ?? '');
        $trangthai = trim($_POST['trangthai'] ?? 'cho_duyet');
        $authorId = (int)($_SESSION['admin_id'] ?? 0);

        if (empty($tieude) || $danhmuc <= 0 || empty($noidung)) {
            echo json_encode(['status' => 'error', 'message' => 'Vui lòng nhập đầy đủ Tiêu đề, Danh mục và Nội dung.']);
            exit;
        }

        $hinhanh = '';
        if (isset($_FILES['hinhanh']) && $_FILES['hinhanh']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['hinhanh']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (!in_array($ext, $allowed)) {
                echo json_encode(['status' => 'error', 'message' => 'Định dạng ảnh không hợp lệ']);
                exit;
            }
            $hinhanh = time() . '_' . rand(100, 999) . '.' . $ext;
            $uploadDir = dirname(APPROOT) . '/assets/images/news/';
            move_uploaded_file($_FILES['hinhanh']['tmp_name'], $uploadDir . $hinhanh);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Vui lòng chọn ảnh đại diện.']);
            exit;
        }

        $newsModel = $this->model('NewsModel');
        $newsModel->create($tieude, $tomtat, $noidung, $danhmuc, $trangthai, $hinhanh, $authorId);
        
        echo json_encode(['status' => 'success', 'message' => 'Thành công']);
        exit;
    }

    public function update()
    {
        header('Content-Type: application/json');
        
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'ID không hợp lệ']);
            exit;
        }

        $tieude = trim($_POST['tieude'] ?? '');
        $danhmuc = (int)($_POST['danhmuc'] ?? 0);
        $tomtat = trim($_POST['tomtat'] ?? '');
        $noidung = trim($_POST['noidung'] ?? '');
        $trangthai = trim($_POST['trangthai'] ?? 'cho_duyet');

        if (empty($tieude) || $danhmuc <= 0 || empty($noidung)) {
            echo json_encode(['status' => 'error', 'message' => 'Vui lòng nhập đủ các trường yêu cầu.']);
            exit;
        }

        $newsModel = $this->model('NewsModel');
        $post = $newsModel->getById($id);
        if (!$post) {
            echo json_encode(['status' => 'error', 'message' => 'Không tìm bảng tin.']);
            exit;
        }

        $hinhanh = $post->hinhanh;
        if (isset($_FILES['hinhanh']) && $_FILES['hinhanh']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['hinhanh']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (!in_array($ext, $allowed)) {
                echo json_encode(['status' => 'error', 'message' => 'Ảnh thay thế không hợp lệ']);
                exit;
            }
            $hinhanh = time() . '_' . rand(100, 999) . '.' . $ext;
            $uploadDir = dirname(APPROOT) . '/assets/images/news/';
            move_uploaded_file($_FILES['hinhanh']['tmp_name'], $uploadDir . $hinhanh);

            // Xóa ảnh cũ
            if (!empty($post->hinhanh) && file_exists($uploadDir . $post->hinhanh)) {
                unlink($uploadDir . $post->hinhanh);
            }
        }

        $newsModel->update($id, $tieude, $tomtat, $noidung, $danhmuc, $trangthai, $hinhanh);
        
        echo json_encode(['status' => 'success']);
        exit;
    }

    public function status()
    {
        header('Content-Type: application/json');
        
        $id = (int)($_POST['id'] ?? 0);
        $action = $_POST['status_action'] ?? '';

        if ($id <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Thiếu ID']);
            exit;
        }

        $newStatus = '';
        if ($action === 'approve') $newStatus = 'da_dang';
        else if ($action === 'hide') $newStatus = 'ban_nhap';

        if (!$newStatus) {
            echo json_encode(['status' => 'error', 'message' => 'Hành động không xác định']);
            exit;
        }

        $newsModel = $this->model('NewsModel');
        $newsModel->updateStatus($id, $newStatus);

        echo json_encode(['status' => 'success']);
        exit;
    }

    public function delete($id = 0)
    {
        header('Content-Type: application/json');
        $newsModel = $this->model('NewsModel');
        $uploadDir = dirname(APPROOT) . '/assets/images/news/';

        // Xóa một bài
        if ($id > 0) {
            $post = $newsModel->getAuthorAndImage((int)$id);
            if ($post) {
                if (!empty($post->hinhanh) && file_exists($uploadDir . $post->hinhanh)) {
                    unlink($uploadDir . $post->hinhanh);
                }
                $newsModel->deleteById((int)$id);
            }
            echo json_encode(['status' => 'success']);
            exit;
        }

        // Xóa nhiều bài
        $input = json_decode(file_get_contents('php://input'), true);
        $ids = array_map('intval', $input['ids'] ?? []);

        if (empty($ids)) {
            echo json_encode(['status' => 'error', 'message' => 'Không có ID hợp lệ']);
            exit;
        }

        $images = $newsModel->getImagesByIds($ids);
        foreach ($images as $imgRow) {
            if (!empty($imgRow->hinhanh) && file_exists($uploadDir . $imgRow->hinhanh)) {
                unlink($uploadDir . $imgRow->hinhanh);
            }
        }

        $newsModel->deleteByIds($ids);
        echo json_encode(['status' => 'success']);
        exit;
    }
}
