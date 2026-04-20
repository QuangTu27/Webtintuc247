<?php

class NewsController extends Controller
{
    public function index($newsId = 0)
    {
        if (!$newsId) {
            header('Location: ' . URLROOT);
            exit;
        }

        $data = $this->getClientViewData();
        $data['newsId'] = (int)$newsId;

        $this->view('site/layouts/header', $data);
        $this->view('site/news_detail', $data);
        $this->view('site/layouts/footer');
    }

}

