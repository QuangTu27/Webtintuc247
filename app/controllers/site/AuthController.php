<?php

class AuthController extends Controller
{
    public function login()
    {
        if (isset($_SESSION['client_logged_in']) && $_SESSION['client_logged_in'] === true) {
            header('Location: ' . URLROOT);
            exit;
        }
        $this->view('site/auth/login');
    }

    public function logout()
    {
        foreach ($_SESSION as $key => $value) {
            if (strpos($key, 'client_') === 0) {
                unset($_SESSION[$key]);
            }
        }
        header('Location: ' . URLROOT);
        exit;
    }
}
