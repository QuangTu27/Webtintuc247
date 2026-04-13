<?php

class NewsModel
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getCategoriesWithParent(): array
    {
        return $this->db->fetchAll(
            "SELECT c.*, p.name AS parent_name
             FROM tbl_categories c
             LEFT JOIN tbl_categories p ON c.parent_id = p.id
             ORDER BY c.parent_id ASC, c.id ASC"
        );
    }

    public function getById(int $id): mixed
    {
        return $this->db->fetch(
            "SELECT * FROM tbl_news WHERE id = ?",
            [$id]
        );
    }

    public function getAuthorAndImage(int $id): mixed
    {
        return $this->db->fetch(
            "SELECT author_id, hinhanh FROM tbl_news WHERE id = ?",
            [$id]
        );
    }

    public function getAll(int $categoryId = 0): array
    {
        $params = [];
        $where = "";

        if ($categoryId > 0) {
            $where = "WHERE n.category_id = ?";
            $params[] = $categoryId;
        }

        return $this->db->fetchAll(
            "SELECT n.*, c.name AS category_name, u.hoten AS author_name, u.username
             FROM tbl_news n
             LEFT JOIN tbl_categories c ON n.category_id = c.id
             LEFT JOIN tbl_users u ON n.author_id = u.id
             $where
             ORDER BY n.id DESC",
            $params
        );
    }

    public function getImagesByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        return $this->db->fetchAll(
            "SELECT hinhanh FROM tbl_news WHERE id IN ($placeholders)",
            $ids
        );
    }

    public function create(string $tieude, string $tomtat, string $noidung, int $categoryId, string $trangthai, string $hinhanh, int $authorId): bool
    {
        $this->db->query(
            "INSERT INTO tbl_news (tieude, tomtat, noidung, category_id, trangthai, hinhanh, author_id, ngaydang)
             VALUES (?, ?, ?, ?, ?, ?, ?, NOW())",
            [$tieude, $tomtat, $noidung, $categoryId, $trangthai, $hinhanh, $authorId]
        );
        return true;
    }

    public function update(int $id, string $tieude, string $tomtat, string $noidung, int $categoryId, string $trangthai, string $hinhanh): bool
    {
        $this->db->query(
            "UPDATE tbl_news
             SET tieude = ?, tomtat = ?, noidung = ?, category_id = ?, trangthai = ?, hinhanh = ?, ngaydang = NOW()
             WHERE id = ?",
            [$tieude, $tomtat, $noidung, $categoryId, $trangthai, $hinhanh, $id]
        );
        return true;
    }

    public function updateStatus(int $id, string $trangthai): bool
    {
        $this->db->query(
            "UPDATE tbl_news SET trangthai = ? WHERE id = ?",
            [$trangthai, $id]
        );
        return true;
    }

    public function deleteById(int $id): bool
    {
        $this->db->query(
            "DELETE FROM tbl_news WHERE id = ?",
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
            "DELETE FROM tbl_news WHERE id IN ($placeholders)",
            $ids
        );
        return true;
    }
}
