<?php

class AdminProfileModel
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getById(int $userId): mixed
    {
        return $this->db->fetch(
            "SELECT id, username, hoten, email, role, avatar, created_at FROM tbl_users WHERE id = ?",
            [$userId]
        );
    }

    public function update(int $userId, string $hoten, string $email, ?string $avatar = null, ?string $password = null): bool
    {
        if ($avatar !== null && $password !== null) {
            $this->db->query(
                "UPDATE tbl_users SET hoten = ?, email = ?, avatar = ?, password = ? WHERE id = ?",
                [$hoten, $email, $avatar, $password, $userId]
            );
        } elseif ($avatar !== null) {
            $this->db->query(
                "UPDATE tbl_users SET hoten = ?, email = ?, avatar = ? WHERE id = ?",
                [$hoten, $email, $avatar, $userId]
            );
        } elseif ($password !== null) {
            $this->db->query(
                "UPDATE tbl_users SET hoten = ?, email = ?, password = ? WHERE id = ?",
                [$hoten, $email, $password, $userId]
            );
        } else {
            $this->db->query(
                "UPDATE tbl_users SET hoten = ?, email = ? WHERE id = ?",
                [$hoten, $email, $userId]
            );
        }
        return true;
    }
}
