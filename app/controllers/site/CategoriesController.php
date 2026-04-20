<?php

class CategoriesController extends Controller
{
    public function index($catId = 0, $action = null)
    {
        if ($action === 'data') {
        }

        $data = $this->getClientViewData();
        $data['catId'] = (int)$catId;

        $this->view('site/layouts/header', $data);
        $this->view('site/categories', $data);
        $this->view('site/layouts/footer');
    }

}

