<?php

class AuthModel
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findAdminByCredentials(string $username, string $password): mixed
    {
        return $this->db->fetch(
            "SELECT * FROM tbl_users WHERE username = ? AND password = ? AND role NOT IN ('user')",
            [$username, $password]
        );
    }

    public function findUserByCredentials(string $usernameOrEmail, string $password): mixed
    {
        return $this->db->fetch(
            "SELECT * FROM tbl_users WHERE (username = ? OR email = ?) AND password = ?",
            [$usernameOrEmail, $usernameOrEmail, $password]
        );
    }

    public function existsByUsernameOrEmail(string $username, string $email = ''): bool
    {
        if ($email !== '') {
            $row = $this->db->fetch(
                "SELECT id FROM tbl_users WHERE username = ? OR email = ?",
                [$username, $email]
            );
        } else {
            $row = $this->db->fetch(
                "SELECT id FROM tbl_users WHERE username = ?",
                [$username]
            );
        }
        return $row !== false;
    }

    public function register(string $hoten, string $username, ?string $email, string $password): bool
    {
        $this->db->query(
            "INSERT INTO tbl_users (hoten, username, email, password, role) VALUES (?, ?, ?, ?, 'user')",
            [$hoten, $username, $email, $password]
        );
        return true;
    }
}
