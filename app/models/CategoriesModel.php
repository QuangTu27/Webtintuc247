<?php

class CategoriesModel
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getParentCategories(): array
    {
        return $this->db->fetchAll(
            "SELECT id, name FROM tbl_categories WHERE parent_id = 0 ORDER BY id ASC"
        );
    }

    public function getEditors(): array
    {
        return $this->db->fetchAll(
            "SELECT id, hoten, username FROM tbl_users WHERE role = 'editor'"
        );
    }

    public function getById(int $id): mixed
    {
        return $this->db->fetch(
            "SELECT * FROM tbl_categories WHERE id = ?",
            [$id]
        );
    }

    public function getAll(int $filterManagerId = 0, int $filterId = 0, bool $isEditor = false, string $keyword = ''): array
    {
        $conditions = [];
        $params = [];

        if ($isEditor && $filterManagerId > 0) {
            $conditions[] = "(c.manager_id = ? OR p.manager_id = ?)";
            $params[] = $filterManagerId;
            $params[] = $filterManagerId;
        }

        if ($filterId > 0) {
            $conditions[] = "(c.id = ? OR c.parent_id = ?)";
            $params[] = $filterId;
            $params[] = $filterId;
        }

        if ($keyword !== '') {
            $conditions[] = "c.name LIKE ?";
            $params[] = "%$keyword%";
        }

        $where = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

        $sql = "SELECT c.*,
                       u.hoten AS manager_name,
                       p.name AS parent_name,
                       p.manager_id AS parent_manager_id,
                       pu.hoten AS parent_manager_name
                FROM tbl_categories c
                LEFT JOIN tbl_users u ON c.manager_id = u.id
                LEFT JOIN tbl_categories p ON c.parent_id = p.id
                LEFT JOIN tbl_users pu ON p.manager_id = pu.id
                $where
                ORDER BY c.parent_id ASC, c.id ASC";

        return $this->db->fetchAll($sql, $params);
    }

    public function existsByName(string $name, int $excludeId = 0): bool
    {
        if ($excludeId > 0) {
            $row = $this->db->fetch(
                "SELECT id FROM tbl_categories WHERE name = ? AND id != ?",
                [$name, $excludeId]
            );
        } else {
            $row = $this->db->fetch(
                "SELECT id FROM tbl_categories WHERE name = ?",
                [$name]
            );
        }
        return $row !== false;
    }

    public function create(string $name, int $parentId, ?int $managerId): bool
    {
        $this->db->query(
            "INSERT INTO tbl_categories (name, parent_id, manager_id) VALUES (?, ?, ?)",
            [$name, $parentId, $managerId]
        );
        return true;
    }

    public function update(int $id, string $name, int $parentId, ?int $managerId = null): bool
    {
        if ($managerId !== null) {
            $this->db->query(
                "UPDATE tbl_categories SET name = ?, parent_id = ?, manager_id = ? WHERE id = ?",
                [$name, $parentId, $managerId, $id]
            );
        } else {
            $this->db->query(
                "UPDATE tbl_categories SET name = ?, parent_id = ? WHERE id = ?",
                [$name, $parentId, $id]
            );
        }
        return true;
    }

    public function deleteById(int $id): bool
    {
        $this->db->query(
            "DELETE FROM tbl_categories WHERE id = ?",
            [$id]
        );
        return true;
    }

    public function deleteByIds(array $ids): bool
    {
        if (empty($ids)) {
            return false;
        }
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $this->db->query(
            "DELETE FROM tbl_categories WHERE id IN ($placeholders)",
            $ids
        );
        return true;
    }
}

