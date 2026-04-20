<?php

class Categories extends ApiController
{
    public function __construct() {
        $this->requireAdmin();
    }

    private function isAdmin(): bool
    {
        $role = $_SESSION['admin_role'] ?? '';
        return in_array($role, ['admin']);
    }

    private function isEditor(): bool
    {
        $role = $_SESSION['admin_role'] ?? '';
        return in_array($role, ['editor', 'admin']);
    }

    // GET /api/categories
    public function index()
    {
        $catModel = $this->model('CategoriesModel');
        $userId = (int)($_SESSION['admin_id'] ?? 0);
        $role = $_SESSION['admin_role'] ?? 'user';
        
        $isEditorMode = $this->isEditor() && !$this->isAdmin();
        $filterId = isset($_GET['filter_id']) ? (int)$_GET['filter_id'] : 0;
        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

        $categories = $catModel->getAll($userId, $filterId, $isEditorMode, $keyword);
        $parents = $catModel->getParentCategories();

        $canAdd = $this->isEditor();
        $canEdit = $this->isEditor();
        $canDelete = $this->isEditor();

        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'data' => $categories,
            'parents' => $parents,
            'auth' => [
                'canAdd' => $canAdd,
                'canEdit' => $canEdit,
                'canDelete' => $canDelete,
                'isAdmin' => $this->isAdmin(),
                'userId' => $userId,
                'userName' => $_SESSION['admin_hoten'] ?? $_SESSION['admin_username'] ?? ''
            ]
        ]);
        exit;
    }

    // GET /api/categories/formdata
    public function formdata()
    {
        $catModel = $this->model('CategoriesModel');
        $parents = $catModel->getParentCategories();
        $managers = $catModel->getEditors();
        $userId = (int)($_SESSION['admin_id'] ?? 0);

        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'data' => [
                'parents' => $parents,
                'managers' => $managers
            ],
            'auth' => [
                'isAdmin' => $this->isAdmin(),
                'userId' => $userId,
                'userName' => $_SESSION['admin_hoten'] ?? $_SESSION['admin_username'] ?? ''
            ]
        ]);
        exit;
    }

    // GET /api/categories/{id}
    public function show($id = 0)
    {
        $catModel = $this->model('CategoriesModel');
        $item = $catModel->getById((int)$id);

        if (!$item) {
            $this->json('error', 'Không tìm thấy danh mục');
        }

        if (!$this->isAdmin() && $item->manager_id != $_SESSION['admin_id']) {
            $this->json('error', 'Không có quyền truy cập danh mục này');
        }

        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'data' => $item]);
        exit;
    }

    // POST /api/categories
    public function store()
    {
        if (!$this->isEditor()) {
            $this->json('error', 'Không có quyền thêm danh mục');
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $name = trim($input['name'] ?? '');
        $parentId = (int)($input['parent_id'] ?? 0);
        $managerId = isset($input['manager_id']) && $input['manager_id'] > 0 ? (int)$input['manager_id'] : null;

        if (empty($name)) {
            $this->json('error', 'Tên danh mục không được để trống');
        }

        $catModel = $this->model('CategoriesModel');

        if ($catModel->existsByName($name)) {
            $this->json('error', 'Tên danh mục đã tồn tại');
        }

        if (!$this->isAdmin()) {
            $managerId = (int)$_SESSION['admin_id'];
        }

        $catModel->create($name, $parentId, $managerId);

        $this->json('success', 'Thêm danh mục thành công');
    }

    // PUT /api/categories/{id}
    public function update($id = 0)
    {
        if (!$this->isEditor()) {
            $this->json('error', 'Không có quyền sửa danh mục');
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $name = trim($input['name'] ?? '');
        $parentId = (int)($input['parent_id'] ?? 0);
        $managerId = isset($input['manager_id']) && $input['manager_id'] > 0 ? (int)$input['manager_id'] : null;

        if (empty($name)) {
            $this->json('error', 'Tên danh mục không được để trống');
        }

        $catModel = $this->model('CategoriesModel');
        $existing = $catModel->getById((int)$id);

        if (!$existing) {
            $this->json('error', 'Danh mục không tồn tại');
        }

        if (!$this->isAdmin() && $existing->manager_id != $_SESSION['admin_id']) {
             $this->json('error', 'Không được phép sửa danh mục phân cho người khác');
        }
        
        if (!$this->isAdmin()) {
            $managerId = $existing->manager_id;
            $parentId = $existing->parent_id;
        }

        if ($catModel->existsByName($name, (int)$id)) {
            $this->json('error', 'Tên danh mục đã tồn tại');
        }

        $catModel->update((int)$id, $name, $parentId, $managerId);

        $this->json('success', 'Cập nhật danh mục thành công');
    }

    // DELETE /api/categories/{id}
    public function destroy($id = 0)
    {
        if (!$this->isEditor()) {
            $this->json('error', 'Không có quyền xoá');
        }

        $catModel = $this->model('CategoriesModel');

        if ($id > 0) {
            $existing = $catModel->getById((int)$id);
            if (!$existing || (!$this->isAdmin() && $existing->manager_id != $_SESSION['admin_id'])) {
                $this->json('error', 'Không được phép xoá danh mục này');
            }

            $catModel->deleteById((int)$id);
            $this->json('success', 'Đã xoá danh mục');
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $ids = array_map('intval', $input['ids'] ?? []);

        if (empty($ids)) {
            $this->json('error', 'Không có ID hợp lệ');
        }

        if (!$this->isAdmin()) {
            foreach ($ids as $cid) {
                $existing = $catModel->getById((int)$cid);
                if (!$existing || $existing->manager_id != $_SESSION['admin_id']) {
                    $this->json('error', 'Bạn đã chọn danh mục không thuộc quyền quản lý');
                }
            }
        }

        $catModel->deleteByIds($ids);
        $this->json('success', 'Đã xoá ' . count($ids) . ' danh mục');
    }
}

