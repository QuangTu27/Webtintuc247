<?php

class SiteModel
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getActiveAds(): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM tbl_ads WHERE status = 'hien'"
        );
    }

    public function countPublishedNews(): int
    {
        $row = $this->db->fetch("SELECT COUNT(id) as total FROM tbl_news WHERE trangthai = 'da_dang'");
        return (int) ($row->total ?? 0);
    }

    public function getPublishedNews(int $limit, int $offset): array
    {
        return $this->db->fetchAll(
            "SELECT n.id, n.tieude, n.tomtat, n.hinhanh, n.ngaydang, n.view_count, c.name as category_name
             FROM tbl_news n
             LEFT JOIN tbl_categories c ON n.category_id = c.id
             WHERE n.trangthai = 'da_dang'
             ORDER BY n.ngaydang DESC
             LIMIT ? OFFSET ?",
            [$limit, $offset]
        );
    }

    public function getTopViewedNews(int $limit = 5): array
    {
        return $this->db->fetchAll(
            "SELECT id, tieude, hinhanh FROM tbl_news WHERE trangthai = 'da_dang' ORDER BY view_count DESC LIMIT ?",
            [$limit]
        );
    }

    public function getCategoryById(int $id): mixed
    {
        return $this->db->fetch(
            "SELECT id, name, parent_id FROM tbl_categories WHERE id = ?",
            [$id]
        );
    }

    public function getParentCategoryById(int $parentId): mixed
    {
        return $this->db->fetch(
            "SELECT id, name FROM tbl_categories WHERE id = ?",
            [$parentId]
        );
    }

    public function getSubCategories(int $parentId): array
    {
        return $this->db->fetchAll(
            "SELECT id, name FROM tbl_categories WHERE parent_id = ? ORDER BY id ASC",
            [$parentId]
        );
    }

    public function countNewsByCategories(array $categoryIds): int
    {
        if (empty($categoryIds)) {
            return 0;
        }
        $placeholders = implode(',', array_fill(0, count($categoryIds), '?'));
        $row = $this->db->fetch(
            "SELECT COUNT(id) as total FROM tbl_news WHERE trangthai = 'da_dang' AND category_id IN ($placeholders)",
            $categoryIds
        );
        return (int) ($row->total ?? 0);
    }

    public function getNewsByCategories(array $categoryIds, int $limit, int $offset): array
    {
        if (empty($categoryIds)) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($categoryIds), '?'));
        $params = array_merge($categoryIds, [$limit, $offset]);
        return $this->db->fetchAll(
            "SELECT id, tieude, tomtat, hinhanh, ngaydang, view_count
             FROM tbl_news
             WHERE trangthai = 'da_dang' AND category_id IN ($placeholders)
             ORDER BY ngaydang DESC
             LIMIT ? OFFSET ?",
            $params
        );
    }

    public function getTopViewedNewsByCategories(array $categoryIds, int $limit = 5): array
    {
        if (empty($categoryIds)) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($categoryIds), '?'));
        $params = array_merge($categoryIds, [$limit]);
        return $this->db->fetchAll(
            "SELECT id, tieude, hinhanh FROM tbl_news WHERE trangthai = 'da_dang' AND category_id IN ($placeholders) ORDER BY view_count DESC LIMIT ?",
            $params
        );
    }

    public function countSearchResults(string $keyword): int
    {
        $row = $this->db->fetch(
            "SELECT COUNT(id) as total FROM tbl_news WHERE trangthai = 'da_dang' AND (tieude LIKE ? OR noidung LIKE ?)",
            ["%$keyword%", "%$keyword%"]
        );
        return (int) ($row->total ?? 0);
    }

    public function searchNews(string $keyword, int $limit, int $offset): array
    {
        return $this->db->fetchAll(
            "SELECT n.id, n.tieude, n.tomtat, n.hinhanh, n.ngaydang, n.view_count, c.name as category_name
             FROM tbl_news n
             LEFT JOIN tbl_categories c ON n.category_id = c.id
             WHERE n.trangthai = 'da_dang' AND (n.tieude LIKE ? OR n.noidung LIKE ?)
             ORDER BY n.ngaydang DESC
             LIMIT ? OFFSET ?",
            ["%$keyword%", "%$keyword%", $limit, $offset]
        );
    }
}
