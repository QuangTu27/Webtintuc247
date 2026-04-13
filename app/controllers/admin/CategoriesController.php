<?php

class CategoriesController extends Controller
{
    private function requireAdmin(): void
    {
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Chưa đăng nhập']);
            exit;
        }
    }

    private function requireAdminPage(): void
    {
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            header('Location: ' . URLROOT . 'admin/auth/login');
            exit;
        }
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

    public function index()
    {
        $this->requireAdminPage();
        $this->view('admin/layouts/header');
        $this->view('admin/categories/list');
        $this->view('admin/layouts/footer');
    }

    public function add()
    {
        $this->requireAdminPage();
        $data = ['id' => 0];
        $this->view('admin/layouts/header');
        $this->view('admin/categories/add', $data);
        $this->view('admin/layouts/footer');
    }

    public function edit($id = 0)
    {
        $this->requireAdminPage();
        $data = ['id' => (int)$id];
        $this->view('admin/layouts/header');
        $this->view('admin/categories/edit', $data);
        $this->view('admin/layouts/footer');
    }

    public function data()
    {
        header('Content-Type: application/json');
        $this->requireAdmin();

        $catModel = $this->model('CategoriesModel');
        $userId = (int)($_SESSION['admin_id'] ?? 0);
        $role = $_SESSION['admin_role'] ?? 'user';
        
        $isEditorMode = $this->isEditor() && !$this->isAdmin();
        $filterId = isset($_GET['filter_id']) ? (int)$_GET['filter_id'] : 0;
        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

        // Nếu isEditorMode thì CategoryModel->getAll(...) sẽ tự fillter category theo $userId manager của nó.
        $categories = $catModel->getAll($userId, $filterId, $isEditorMode, $keyword);
        $parents = $catModel->getParentCategories();

        $canAdd = $this->isEditor();
        $canEdit = $this->isEditor();
        $canDelete = $this->isEditor();

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

    public function formdata()
    {
        header('Content-Type: application/json');
        $this->requireAdmin();

        $catModel = $this->model('CategoriesModel');
        $parents = $catModel->getParentCategories();
        $managers = $catModel->getEditors();
        $userId = (int)($_SESSION['admin_id'] ?? 0);

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

    public function show($id = 0)
    {
        header('Content-Type: application/json');
        $this->requireAdmin();

        $catModel = $this->model('CategoriesModel');
        $item = $catModel->getById((int)$id);

        if (!$item) {
            echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy danh mục']);
            exit;
        }

        if (!$this->isAdmin() && $item->manager_id != $_SESSION['admin_id']) {
            echo json_encode(['status' => 'error', 'message' => 'Không có quyền truy cập danh mục này']);
            exit;
        }

        echo json_encode(['status' => 'success', 'data' => $item]);
        exit;
    }

    public function store()
    {
        header('Content-Type: application/json');
        $this->requireAdmin();

        if (!$this->isEditor()) {
            echo json_encode(['status' => 'error', 'message' => 'Không có quyền thêm danh mục']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $name = trim($input['name'] ?? '');
        $parentId = (int)($input['parent_id'] ?? 0);
        $managerId = isset($input['manager_id']) && $input['manager_id'] > 0 ? (int)$input['manager_id'] : null;

        if (empty($name)) {
            echo json_encode(['status' => 'error', 'message' => 'Tên danh mục không được để trống']);
            exit;
        }

        $catModel = $this->model('CategoriesModel');

        if ($catModel->existsByName($name)) {
            echo json_encode(['status' => 'error', 'message' => 'Tên danh mục đã tồn tại']);
            exit;
        }

        if (!$this->isAdmin()) {
            $managerId = (int)$_SESSION['admin_id'];
        }

        $catModel->create($name, $parentId, $managerId);

        echo json_encode(['status' => 'success', 'message' => 'Thêm danh mục thành công']);
        exit;
    }

    public function update($id = 0)
    {
        header('Content-Type: application/json');
        $this->requireAdmin();

        if (!$this->isEditor()) {
            echo json_encode(['status' => 'error', 'message' => 'Không có quyền sửa danh mục']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $name = trim($input['name'] ?? '');
        $parentId = (int)($input['parent_id'] ?? 0);
        $managerId = isset($input['manager_id']) && $input['manager_id'] > 0 ? (int)$input['manager_id'] : null;

        if (empty($name)) {
            echo json_encode(['status' => 'error', 'message' => 'Tên danh mục không được để trống']);
            exit;
        }

        $catModel = $this->model('CategoriesModel');
        $existing = $catModel->getById((int)$id);

        if (!$existing) {
            echo json_encode(['status' => 'error', 'message' => 'Danh mục không tồn tại']);
            exit;
        }

        if (!$this->isAdmin() && $existing->manager_id != $_SESSION['admin_id']) {
             echo json_encode(['status' => 'error', 'message' => 'Không được phép sửa danh mục phân cho người khác']);
             exit;
        }
        
        if (!$this->isAdmin()) {
            $managerId = $existing->manager_id;
            $parentId = $existing->parent_id; // Thường editor không được đổi category gốc sang parent khác
        }

        if ($catModel->existsByName($name, (int)$id)) {
            echo json_encode(['status' => 'error', 'message' => 'Tên danh mục đã tồn tại']);
            exit;
        }

        $catModel->update((int)$id, $name, $parentId, $managerId);

        echo json_encode(['status' => 'success', 'message' => 'Cập nhật danh mục thành công']);
        exit;
    }

    public function delete($id = 0)
    {
        header('Content-Type: application/json');
        $this->requireAdmin();

        if (!$this->isEditor()) {
            echo json_encode(['status' => 'error', 'message' => 'Không có quyền xoá']);
            exit;
        }

        $catModel = $this->model('CategoriesModel');

        if ($id > 0) {
            $existing = $catModel->getById((int)$id);
            if (!$existing || (!$this->isAdmin() && $existing->manager_id != $_SESSION['admin_id'])) {
                echo json_encode(['status' => 'error', 'message' => 'Không được phép xoá danh mục này']);
                exit;
            }

            $catModel->deleteById((int)$id);
            echo json_encode(['status' => 'success', 'message' => 'Đã xoá danh mục']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $ids = array_map('intval', $input['ids'] ?? []);

        if (empty($ids)) {
            echo json_encode(['status' => 'error', 'message' => 'Không có ID hợp lệ']);
            exit;
        }

        if (!$this->isAdmin()) {
            foreach ($ids as $cid) {
                $existing = $catModel->getById((int)$cid);
                if (!$existing || $existing->manager_id != $_SESSION['admin_id']) {
                    echo json_encode(['status' => 'error', 'message' => 'Bạn đã chọn danh mục không thuộc quyền quản lý']);
                    exit;
                }
            }
        }

        $catModel->deleteByIds($ids);
        echo json_encode(['status' => 'success', 'message' => 'Đã xoá ' . count($ids) . ' danh mục']);
        exit;
    }
}
