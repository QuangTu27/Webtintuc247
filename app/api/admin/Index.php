<?php

class Index extends ApiController
{
    public function index()
    {
        $this->json('success', [
            'app' => 'WebTinTuc247 API',
            'version' => '1.0.0',
            'developer' => 'Senior Backend',
            'message' => 'API is running normally. Vui lòng truy cập các endpoint hợp lệ.'
        ]);
    }
}

