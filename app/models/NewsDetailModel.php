<?php

class NewsDetailModel
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function incrementViewCount(int $id): void
    {
        $this->db->query(
            "UPDATE tbl_news SET view_count = view_count + 1 WHERE id = ?",
            [$id]
        );
    }

    public function getDetailById(int $id): mixed
    {
        return $this->db->fetch(
            "SELECT n.*, c1.name as cat_name, c1.id as cat_id, c2.name as parent_name, c2.id as parent_id, u.hoten as author_name
             FROM tbl_news n
             LEFT JOIN tbl_categories c1 ON n.category_id = c1.id
             LEFT JOIN tbl_categories c2 ON c1.parent_id = c2.id
             LEFT JOIN tbl_users u ON n.author_id = u.id
             WHERE n.id = ? LIMIT 1",
            [$id]
        );
    }

    public function countLikes(int $newsId): int
    {
        $row = $this->db->fetch(
            "SELECT COUNT(*) as total FROM tbl_likes WHERE news_id = ?",
            [$newsId]
        );
        return (int) ($row->total ?? 0);
    }

    public function isLikedByUser(int $userId, int $newsId): bool
    {
        $row = $this->db->fetch(
            "SELECT id FROM tbl_likes WHERE user_id = ? AND news_id = ?",
            [$userId, $newsId]
        );
        return $row !== false;
    }

    public function isSavedByUser(int $userId, int $newsId): bool
    {
        $row = $this->db->fetch(
            "SELECT id FROM tbl_bookmarks WHERE user_id = ? AND news_id = ?",
            [$userId, $newsId]
        );
        return $row !== false;
    }

    public function getApprovedComments(int $newsId): array
    {
        return $this->db->fetchAll(
            "SELECT c.*, u.avatar FROM tbl_comments c
             LEFT JOIN tbl_users u ON c.user_id = u.id
             WHERE news_id = ? AND status IN (1, 2)
             ORDER BY ngaybinh ASC",
            [$newsId]
        );
    }

    public function toggleLike(int $userId, int $newsId): string
    {
        $existing = $this->db->fetch(
            "SELECT id FROM tbl_likes WHERE user_id = ? AND news_id = ?",
            [$userId, $newsId]
        );

        if ($existing !== false) {
            $this->db->query(
                "DELETE FROM tbl_likes WHERE user_id = ? AND news_id = ?",
                [$userId, $newsId]
            );
            return 'unliked';
        }

        $this->db->query(
            "INSERT INTO tbl_likes (user_id, news_id) VALUES (?, ?)",
            [$userId, $newsId]
        );
        return 'liked';
    }

    public function toggleSave(int $userId, int $newsId): string
    {
        $existing = $this->db->fetch(
            "SELECT id FROM tbl_bookmarks WHERE user_id = ? AND news_id = ?",
            [$userId, $newsId]
        );

        if ($existing !== false) {
            $this->db->query(
                "DELETE FROM tbl_bookmarks WHERE user_id = ? AND news_id = ?",
                [$userId, $newsId]
            );
            return 'unsaved';
        }

        $this->db->query(
            "INSERT INTO tbl_bookmarks (user_id, news_id, ngay_luu) VALUES (?, ?, NOW())",
            [$userId, $newsId]
        );
        return 'saved';
    }

    public function postComment(int $newsId, int $userId, string $tenNguoiBinh, string $noidung, ?int $parentId, int $status): bool
    {
        $this->db->query(
            "INSERT INTO tbl_comments (news_id, user_id, ten_nguoi_binh, noidung, parent_id, status) VALUES (?, ?, ?, ?, ?, ?)",
            [$newsId, $userId, $tenNguoiBinh, $noidung, $parentId, $status]
        );
        return true;
    }

    public function getCommentByIdAndUser(int $commentId, int $userId): mixed
    {
        return $this->db->fetch(
            "SELECT id FROM tbl_comments WHERE id = ? AND user_id = ? AND status = 1",
            [$commentId, $userId]
        );
    }

    public function editComment(int $commentId, string $noidung): bool
    {
        $this->db->query(
            "UPDATE tbl_comments SET noidung = ? WHERE id = ?",
            [$noidung, $commentId]
        );
        return true;
    }

    public function softDeleteComment(int $commentId): bool
    {
        $this->db->query(
            "UPDATE tbl_comments SET status = 2 WHERE id = ?",
            [$commentId]
        );
        return true;
    }

    public function isCommentOwnedByUser(int $commentId, int $userId): bool
    {
        $row = $this->db->fetch(
            "SELECT id FROM tbl_comments WHERE id = ? AND user_id = ?",
            [$commentId, $userId]
        );
        return $row !== false;
    }
}
