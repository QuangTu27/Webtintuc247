<?php

class UserProfileModel
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getInfo(int $userId): mixed
    {
        return $this->db->fetch(
            "SELECT id, username, hoten, email, avatar, created_at FROM tbl_users WHERE id = ?",
            [$userId]
        );
    }

    // --- Avatar ---
    public function updateAvatar(int $userId, string $avatar): bool
    {
        $this->db->query("UPDATE tbl_users SET avatar = ? WHERE id = ?", [$avatar, $userId]);
        return true;
    }

    public function getAvatarById(int $userId): mixed
    {
        return $this->db->fetch("SELECT avatar FROM tbl_users WHERE id = ?", [$userId]);
    }

    public function updateName(int $userId, string $hoten): bool
    {
        $this->db->query("UPDATE tbl_users SET hoten = ? WHERE id = ?", [$hoten, $userId]);
        return true;
    }

    public function updateEmail(int $userId, string $email): bool
    {
        $this->db->query("UPDATE tbl_users SET email = ? WHERE id = ?", [$email, $userId]);
        return true;
    }

    public function emailExistsForOther(string $email, int $excludeId): bool
    {
        $row = $this->db->fetch(
            "SELECT id FROM tbl_users WHERE email = ? AND id != ?",
            [$email, $excludeId]
        );
        return $row !== false;
    }

    public function updatePassword(int $userId, string $hashedPassword): bool
    {
        $this->db->query("UPDATE tbl_users SET password = ? WHERE id = ?", [$hashedPassword, $userId]);
        return true;
    }

    public function getPasswordById(int $userId): mixed
    {
        return $this->db->fetch("SELECT password FROM tbl_users WHERE id = ?", [$userId]);
    }

    public function getHistory(int $userId): array
    {
        $cookieName = 'viewed_news_' . $userId;
        $ids = isset($_COOKIE[$cookieName]) ? json_decode($_COOKIE[$cookieName], true) : [];
        $ids = array_filter(array_map('intval', (array)$ids));
        return $this->getViewedNewsByIds($ids);
    }

    private function getViewedNewsByIds(array $ids): array
    {
        if (empty($ids)) return [];
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $orderedIds = implode(',', array_map('intval', $ids));
        return $this->db->fetchAll(
            "SELECT n.id, n.tieude, n.hinhanh, n.ngaydang, c.name as cat_name
             FROM tbl_news n
             LEFT JOIN tbl_categories c ON n.category_id = c.id
             WHERE n.id IN ($placeholders)
             ORDER BY FIELD(n.id, $orderedIds)",
            $ids
        );
    }

    public function getBookmarks(int $userId): array
    {
        return $this->db->fetchAll(
            "SELECT n.id, n.tieude, n.hinhanh, b.ngay_luu
             FROM tbl_bookmarks b
             JOIN tbl_news n ON b.news_id = n.id
             WHERE b.user_id = ?
             ORDER BY b.ngay_luu DESC",
            [$userId]
        );
    }

    public function toggleBookmark(int $userId, int $newsId): bool
    {
        $existing = $this->db->fetch(
            "SELECT id FROM tbl_bookmarks WHERE user_id = ? AND news_id = ?",
            [$userId, $newsId]
        );
        if ($existing) {
            $this->db->query("DELETE FROM tbl_bookmarks WHERE user_id = ? AND news_id = ?", [$userId, $newsId]);
            return false; // unsaved
        } else {
            $this->db->query(
                "INSERT INTO tbl_bookmarks (user_id, news_id, ngay_luu) VALUES (?, ?, NOW())",
                [$userId, $newsId]
            );
            return true; // saved
        }
    }

    public function deleteBookmark(int $userId, int $newsId): bool
    {
        $this->db->query("DELETE FROM tbl_bookmarks WHERE user_id = ? AND news_id = ?", [$userId, $newsId]);
        return true;
    }

    public function getComments(int $userId): array
    {
        return $this->db->fetchAll(
            "SELECT c.id, c.noidung, c.ngaybinh, c.status, n.tieude as news_title, n.id as news_id, n.hinhanh
             FROM tbl_comments c
             JOIN tbl_news n ON c.news_id = n.id
             WHERE c.user_id = ?
             ORDER BY c.ngaybinh DESC",
            [$userId]
        );
    }

    public function deleteComment(int $commentId, int $userId): bool
    {
        $row = $this->db->fetch(
            "SELECT id FROM tbl_comments WHERE id = ? AND user_id = ?",
            [$commentId, $userId]
        );
        if ($row) {
            $this->db->query("DELETE FROM tbl_comments WHERE id = ?", [$commentId]);
            return true;
        }
        return false;
    }

}
