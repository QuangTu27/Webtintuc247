<?php

class App
{
    protected  $controller = 'HomeController';
    protected  $method = 'index';
    protected array $params = [];
    protected string $namespace = 'site';

    public function __construct()
    {
        $url = $this->parseUrl();

        // Xác định không gian làm việc (namespace: site dường như mặc định, admin nếu url bắt đầu bằng 'admin')
        if (isset($url[0]) && strtolower($url[0]) === 'admin') {
            $this->namespace = 'admin';
            $this->controller = 'DashboardController';
            unset($url[0]);
            $url = array_values($url);
        }

        // Tìm controller file
        $controllerName = isset($url[0]) ? ucfirst($url[0]) . 'Controller' : $this->controller;
        $controllerFile = APPROOT . '/controllers/' . $this->namespace . '/' . $controllerName . '.php';

        if (isset($url[0]) && file_exists($controllerFile)) {
            $this->controller = $controllerName;
            unset($url[0]);
            $url = array_values($url);
        } else {
            $controllerFile = APPROOT . '/controllers/' . $this->namespace . '/' . $this->controller . '.php';
        }

        if (file_exists($controllerFile)) {
            require_once $controllerFile;
            $this->controller = new $this->controller();
            
            // Tìm method (hàm)
            if (isset($url[0]) && method_exists($this->controller, $url[0])) {
                $this->method = $url[0];
                unset($url[0]);
                $url = array_values($url);
            }
        } else {
            die("Controller file not found: " . $controllerFile);
        }

        // Lấy params
        $this->params = $url ? array_values($url) : [];

        // Gọi method và truyền params
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    protected function parseUrl(): array
    {
        if (isset($_GET['url']) && $_GET['url'] !== '') {
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }
        return [];
    }
}
