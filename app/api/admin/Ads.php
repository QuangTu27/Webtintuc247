<?php

class Ads extends ApiController
{
    public function __construct() {
        $this->requireAdmin();
        $this->checkPermission();
    }

    private function checkPermission()
    {
        $role = $_SESSION['admin_role'] ?? '';
        if ($role !== 'admin') {
            $this->json('error', 'Bạn không có quyền quản lý Quảng cáo');
        }
    }

    // GET /api/ads
    public function index()
    {
        $adsModel = $this->model('AdsModel');
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $ads = $adsModel->getAll($search);

        $this->json('success', $ads);
    }

    // GET /api/ads/{id}
    public function show($id = 0)
    {
        $adsModel = $this->model('AdsModel');
        $ad = $adsModel->getById((int)$id);

        if (!$ad) {
            $this->json('error', 'Không tìm thấy quảng cáo');
        }

        $this->json('success', $ad);
    }

    // POST /api/ads
    public function store()
    {
        $title = trim($_POST['title'] ?? '');
        $link = trim($_POST['link'] ?? '');
        $position = trim($_POST['position'] ?? 'inline_home');
        $status = trim($_POST['status'] ?? 'an');

        if (empty($title)) {
            $this->json('error', 'Vui lòng nhập tiêu đề');
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
                $this->json('error', 'Định dạng file không hỗ trợ');
            }
            
            $mediaFile = time() . '_' . rand(1000, 9999) . '.' . $ext;
            $uploadDir = dirname(APPROOT) . '/assets/images/ads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            move_uploaded_file($tmpName, $uploadDir . $mediaFile);
        } else {
            $this->json('error', 'Vui lòng chọn Hình ảnh hoặc Video');
        }

        $adsModel = $this->model('AdsModel');
        $adsModel->create($title, $mediaFile, $mediaType, $link, $position, $status);

        $this->json('success', 'Thêm quảng cáo thành công');
    }

    // POST /api/ads/{id} 
    public function update($id = 0)
    {
        $id = (int)$id;
        if ($id <= 0 && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
        }

        $title = trim($_POST['title'] ?? '');
        $link = trim($_POST['link'] ?? '');
        $position = trim($_POST['position'] ?? 'inline_home');
        $status = trim($_POST['status'] ?? 'an');

        if (empty($title)) {
            $this->json('error', 'Vui lòng nhập tiêu đề');
        }

        $adsModel = $this->model('AdsModel');
        $existingAd = $adsModel->getById($id);
        
        if (!$existingAd) {
            $this->json('error', 'Không tìm thấy quảng cáo');
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
                $this->json('error', 'Định dạng file không hỗ trợ');
            }
            
            $newMediaFile = time() . '_' . rand(1000, 9999) . '.' . $ext;
            $uploadDir = dirname(APPROOT) . '/assets/images/ads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            move_uploaded_file($tmpName, $uploadDir . $newMediaFile);
            
            if (!empty($existingAd->media_file) && file_exists($uploadDir . $existingAd->media_file)) {
                unlink($uploadDir . $existingAd->media_file);
            }
            
            $mediaFile = $newMediaFile;
        }

        $adsModel->update($id, $title, $mediaFile, $mediaType, $link, $position, $status);

        $this->json('success', 'Cập nhật thành công');
    }

    // DELETE /api/ads/{id}
    public function destroy($id = 0)
    {
        $adsModel = $this->model('AdsModel');
        $uploadDir = dirname(APPROOT) . '/assets/images/ads/';

        if ($id > 0) {
            $ad = $adsModel->getById((int)$id);
            if ($ad && !empty($ad->media_file) && file_exists($uploadDir . $ad->media_file)) {
                unlink($uploadDir . $ad->media_file);
            }
            $adsModel->deleteById((int)$id);
            $this->json('success', 'Đã xoá quảng cáo');
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $ids = array_map('intval', $input['ids'] ?? []);

        if (empty($ids)) {
            $this->json('error', 'Không có ID hợp lệ');
        }

        foreach ($ids as $adId) {
            $ad = $adsModel->getById($adId);
            if ($ad && !empty($ad->media_file) && file_exists($uploadDir . $ad->media_file)) {
                unlink($uploadDir . $ad->media_file);
            }
        }

        $adsModel->deleteByIds($ids);
        $this->json('success', 'Đã xoá các quảng cáo');
    }
}

