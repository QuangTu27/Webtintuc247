<?php

class DashboardController extends Controller
{
    public function index()
    {
        $this->view('admin/layouts/header');
        $this->view('admin/dashboard');
        $this->view('admin/layouts/footer');
    }

}
