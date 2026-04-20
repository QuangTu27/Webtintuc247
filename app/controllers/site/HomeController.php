<?php

class HomeController extends Controller
{
    public function index()
    {
        $data = $this->getClientViewData();

        $this->view('site/layouts/header', $data);
        $this->view('site/home');
        $this->view('site/layouts/footer');
    }

}

