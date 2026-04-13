<?php

class AdsModel
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getById(int $id): mixed
    {
        return $this->db->fetch(
            "SELECT * FROM tbl_ads WHERE id = ?",
            [$id]
        );
    }

    public function getAll(string $search = ''): array
    {
        if ($search !== '') {
            return $this->db->fetchAll(
                "SELECT * FROM tbl_ads
                 WHERE title LIKE ? OR position LIKE ? OR link LIKE ?
                 ORDER BY id ASC",
                ["%$search%", "%$search%", "%$search%"]
            );
        }
        return $this->db->fetchAll(
            "SELECT * FROM tbl_ads ORDER BY id ASC"
        );
    }

    public function create(string $title, string $mediaFile, string $mediaType, string $link, string $position, string $status): bool
    {
        $this->db->query(
            "INSERT INTO tbl_ads (title, media_file, media_type, link, position, status)
             VALUES (?, ?, ?, ?, ?, ?)",
            [$title, $mediaFile, $mediaType, $link, $position, $status]
        );
        return true;
    }

    public function update(int $id, string $title, string $mediaFile, string $mediaType, string $link, string $position, string $status): bool
    {
        $this->db->query(
            "UPDATE tbl_ads
             SET title = ?, media_file = ?, media_type = ?, link = ?, position = ?, status = ?
             WHERE id = ?",
            [$title, $mediaFile, $mediaType, $link, $position, $status, $id]
        );
        return true;
    }

    public function deleteById(int $id): bool
    {
        $this->db->query(
            "DELETE FROM tbl_ads WHERE id = ?",
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
            "DELETE FROM tbl_ads WHERE id IN ($placeholders)",
            $ids
        );
        return true;
    }
}
