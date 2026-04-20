<?php

class SearchController extends Controller
{
    public function index()
    {
        $data = $this->getClientViewData();
        $this->view('site/layouts/header', $data);
        $this->view('site/search', $data);
        $this->view('site/layouts/footer');
    }

}
