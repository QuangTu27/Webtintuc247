<?php

class AdsController extends Controller
{
    public function __construct() {
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            header('Location: ' . URLROOT . 'admin/auth/login'); 
            exit;
        }
        
        $role = $_SESSION['admin_role'] ?? '';
        
        if ($role !== 'admin') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'DELETE' || isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Bạn không có quyền quản lý Quảng cáo']);
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
        $this->view('admin/ads/list');
        $this->view('admin/layouts/footer');
    }

    public function add()
    {
        $this->view('admin/layouts/header');
        $this->view('admin/ads/add');
        $this->view('admin/layouts/footer');
    }

    public function edit($id = 0)
    {
        $data = ['id' => (int)$id];
        $this->view('admin/layouts/header');
        $this->view('admin/ads/edit', $data);
        $this->view('admin/layouts/footer');
    }

    public function data()
    {
        header('Content-Type: application/json');
        
        $adsModel = $this->model('AdsModel');
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        
        $ads = $adsModel->getAll($search);

        echo json_encode([
            'status' => 'success',
            'data' => $ads
        ]);
        exit;
    }

    public function show($id = 0)
    {
        header('Content-Type: application/json');

        $adsModel = $this->model('AdsModel');
        $ad = $adsModel->getById((int)$id);

        if (!$ad) {
            echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy quảng cáo']);
            exit;
        }

        echo json_encode(['status' => 'success', 'data' => $ad]);
        exit;
    }

    public function store()
    {
        header('Content-Type: application/json');

        $title = trim($_POST['title'] ?? '');
        $link = trim($_POST['link'] ?? '');
        $position = trim($_POST['position'] ?? 'inline_home');
        $status = trim($_POST['status'] ?? 'an');

        if (empty($title)) {
            echo json_encode(['status' => 'error', 'message' => 'Vui lòng nhập tiêu đề']);
            exit;
        }

        $mediaFile = '';
        $mediaType = 'image';

        if (isset($_FILES['media_file']) && $_FILES['media_file']['error'] === UPLOAD_ERR_OK) {
            $tmpName = $_FILES['media_file']['tmp_name'];
            $fileInfo = pathinfo($_FILES['media_file']['name']);
            $ext = strtolower($fileInfo['extension']);
            
            $allowedImages = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
            $allowedVideos = ['mp4', 'webm', 'ogg'];
            
            if (in_array($ext, $allowedImages)) {
                $mediaType = 'image';
            } elseif (in_array($ext, $allowedVideos)) {
                $mediaType = 'video';
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Định dạng file không hỗ trợ']);
                exit;
            }
            
            $mediaFile = time() . '_' . rand(1000, 9999) . '.' . $ext;
            $uploadDir = dirname(APPROOT) . '/assets/images/ads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            move_uploaded_file($tmpName, $uploadDir . $mediaFile);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Vui lòng chọn Hình ảnh hoặc Video']);
            exit;
        }

        $adsModel = $this->model('AdsModel');
        $adsModel->create($title, $mediaFile, $mediaType, $link, $position, $status);

        echo json_encode(['status' => 'success', 'message' => 'Thêm quảng cáo thành công']);
        exit;
    }

    public function update($id = 0)
    {
        header('Content-Type: application/json');
        
        $id = (int)$id;
        if ($id <= 0 && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
        }

        $title = trim($_POST['title'] ?? '');
        $link = trim($_POST['link'] ?? '');
        $position = trim($_POST['position'] ?? 'inline_home');
        $status = trim($_POST['status'] ?? 'an');

        if (empty($title)) {
            echo json_encode(['status' => 'error', 'message' => 'Vui lòng nhập tiêu đề']);
            exit;
        }

        $adsModel = $this->model('AdsModel');
        $existingAd = $adsModel->getById($id);
        
        if (!$existingAd) {
            echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy quảng cáo']);
            exit;
        }
        
        $mediaFile = $existingAd->media_file;
        $mediaType = $existingAd->media_type;

        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
            $tmpName = $_FILES['image_file']['tmp_name'];
            $fileInfo = pathinfo($_FILES['image_file']['name']);
            $ext = strtolower($fileInfo['extension']);
            
            $allowedImages = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
            $allowedVideos = ['mp4', 'webm', 'ogg'];
            
            if (in_array($ext, $allowedImages)) {
                $mediaType = 'image';
            } elseif (in_array($ext, $allowedVideos)) {
                $mediaType = 'video';
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Định dạng file không hỗ trợ']);
                exit;
            }
            
            $newMediaFile = time() . '_' . rand(1000, 9999) . '.' . $ext;
            $uploadDir = dirname(APPROOT) . '/assets/images/ads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            move_uploaded_file($tmpName, $uploadDir . $newMediaFile);
            
            // Xóa file cũ
            if (!empty($existingAd->media_file) && file_exists($uploadDir . $existingAd->media_file)) {
                unlink($uploadDir . $existingAd->media_file);
            }
            
            $mediaFile = $newMediaFile;
        }

        $adsModel->update($id, $title, $mediaFile, $mediaType, $link, $position, $status);

        echo json_encode(['status' => 'success', 'message' => 'Cập nhật thành công']);
        exit;
    }

    public function delete($id = 0)
    {
        header('Content-Type: application/json');
        
        $adsModel = $this->model('AdsModel');
        $uploadDir = dirname(APPROOT) . '/assets/images/ads/';

        if ($id > 0) {
            $ad = $adsModel->getById((int)$id);
            if ($ad && !empty($ad->media_file) && file_exists($uploadDir . $ad->media_file)) {
                unlink($uploadDir . $ad->media_file);
            }
            $adsModel->deleteById((int)$id);
            echo json_encode(['status' => 'success', 'message' => 'Đã xoá quảng cáo']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $ids = array_map('intval', $input['ids'] ?? []);

        if (empty($ids)) {
            echo json_encode(['status' => 'error', 'message' => 'Không có ID hợp lệ']);
            exit;
        }

        foreach ($ids as $adId) {
            $ad = $adsModel->getById($adId);
            if ($ad && !empty($ad->media_file) && file_exists($uploadDir . $ad->media_file)) {
                unlink($uploadDir . $ad->media_file);
            }
        }

        $adsModel->deleteByIds($ids);
        echo json_encode(['status' => 'success', 'message' => 'Đã xoá các quảng cáo']);
        exit;
    }
}
