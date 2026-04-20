<?php

class AuthController extends Controller
{
    public function index()
    {
        $this->login();
    }
    
    public function login()
    {
        if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
            header('Location: ' . URLROOT . 'admin/dashboard');
            exit;
        }
        $this->view('admin/auth/login');
    }


    public function logout()
    {
        foreach ($_SESSION as $key => $value) {
            if (strpos($key, 'admin_') === 0) {
                unset($_SESSION[$key]);
            }
        }
        header('Location: ' . URLROOT . 'admin/auth/login');
        exit;
    }
}
