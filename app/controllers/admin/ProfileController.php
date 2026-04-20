<?php

class ProfileController extends Controller
{
    public function __construct() {
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            header('Location: ' . URLROOT . 'admin/auth/login'); 
            exit;
        }
    }

    public function index()
    {
        $this->view('admin/layouts/header');
        $this->view('admin/profile');
        $this->view('admin/layouts/footer');
    }

}
