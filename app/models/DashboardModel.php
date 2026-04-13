<?php

class DashboardModel
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function countNews(): int
    {
        $row = $this->db->fetch("SELECT COUNT(*) as total FROM tbl_news");
        return (int) ($row->total ?? 0);
    }

    public function countPendingNews(): int
    {
        $row = $this->db->fetch("SELECT COUNT(*) as total FROM tbl_news WHERE trangthai = 'cho_duyet'");
        return (int) ($row->total ?? 0);
    }

    public function countCategories(): int
    {
        $row = $this->db->fetch("SELECT COUNT(*) as total FROM tbl_categories");
        return (int) ($row->total ?? 0);
    }

    public function countUsers(): int
    {
        $row = $this->db->fetch("SELECT COUNT(*) as total FROM tbl_users");
        return (int) ($row->total ?? 0);
    }

    public function getLatestPendingNews(int $limit = 5): array
    {
        return $this->db->fetchAll(
            "SELECT id, tieude, ngaydang, author_id FROM tbl_news WHERE trangthai = 'cho_duyet' ORDER BY id DESC LIMIT ?",
            [$limit]
        );
    }
}
