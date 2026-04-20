<?php

class ApiController extends Controller
{

    protected function json($status, $dataOrMessage = null)
    {
        header('Content-Type: application/json');
        $response = ['status' => $status];
        
        if ($status === 'success') {
            if ($dataOrMessage !== null) {
                $response['data'] = $dataOrMessage;
            }
        } else {
            $response['message'] = $dataOrMessage ?? 'Lỗi không xác định';
        }
        
        echo json_encode($response);
        exit;
    }


    protected function jsonRaw(array $response)
    {
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }


    protected function requireAdmin()
    {
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            $this->json('error', 'Unauthorized - Vui lòng đăng nhập quyền quản trị (Admin).');
        }
    }


    protected function requireUser()
    {
        if (!isset($_SESSION['client_logged_in']) || $_SESSION['client_logged_in'] !== true) {
            $this->json('error', 'Unauthorized - Vui lòng đăng nhập người dùng.');
        }
    }
}
