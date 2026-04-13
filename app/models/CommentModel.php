<?php

class CommentModel
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getNewsTitleById(int $newsId): mixed
    {
        return $this->db->fetch(
            "SELECT tieude FROM tbl_news WHERE id = ?",
            [$newsId]
        );
    }

    public function getByNewsId(int $newsId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM tbl_comments WHERE news_id = ? ORDER BY parent_id ASC, create_at DESC",
            [$newsId]
        );
    }

    public function getNewsListWithCommentCount(int $filterManagerId = 0, int $filterCatId = 0, string $search = '', bool $isEditor = false): array
    {
        $conditions = [];
        $params = [];

        if ($isEditor && $filterManagerId > 0) {
            $conditions[] = "(cat.manager_id = ? OR parent_cat.manager_id = ?)";
            $params[] = $filterManagerId;
            $params[] = $filterManagerId;
        }

        if ($filterCatId > 0) {
            $conditions[] = "(n.category_id = ? OR cat.parent_id = ?)";
            $params[] = $filterCatId;
            $params[] = $filterCatId;
        }

        if ($search !== '') {
            $conditions[] = "n.tieude LIKE ?";
            $params[] = "%$search%";
        }

        $where = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

        return $this->db->fetchAll(
            "SELECT n.id, n.tieude, n.ngaydang, cat.name AS cat_name, parent_cat.name AS parent_name, COUNT(c.id) AS total_comments
             FROM tbl_news n
             LEFT JOIN tbl_categories cat ON n.category_id = cat.id
             LEFT JOIN tbl_categories parent_cat ON cat.parent_id = parent_cat.id
             LEFT JOIN tbl_comments c ON n.id = c.news_id
             $where
             GROUP BY n.id
             ORDER BY n.ngaydang DESC",
            $params
        );
    }

    public function getParentCategories(): array
    {
        return $this->db->fetchAll(
            "SELECT id, name FROM tbl_categories WHERE parent_id = 0 ORDER BY name ASC"
        );
    }

    public function deleteById(int $id): bool
    {
        $this->db->query(
            "DELETE FROM tbl_comments WHERE id = ?",
            [$id]
        );
        return true;
    }
}
