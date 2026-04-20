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

        if (isset($url[0])) {
            if (strtolower($url[0]) === 'api') {
                $this->namespace = 'api/admin';
                $this->controller = 'Index'; 
                unset($url[0]);
                $url = array_values($url);

                if (isset($url[0])) {
                    if (strtolower($url[0]) === 'site') {
                        $this->namespace = 'api/site';
                        unset($url[0]);
                        $url = array_values($url);
                    } elseif (strtolower($url[0]) === 'admin') {
                        $this->namespace = 'api/admin';
                        unset($url[0]);
                        $url = array_values($url);
                    }
                }
            } elseif (strtolower($url[0]) === 'admin') {
                $this->namespace = 'admin';
                $this->controller = 'DashboardController';
                unset($url[0]);
                $url = array_values($url);
            }
        }

        if (str_starts_with($this->namespace, 'api')) {
            $controllerDir = APPROOT . '/' . $this->namespace . '/';
        } else {
            $controllerDir = APPROOT . '/controllers/' . $this->namespace . '/';
        }

        if (str_starts_with($this->namespace, 'api')) {
            $controllerName = isset($url[0]) ? ucfirst($url[0]) : $this->controller;
        } else {
            $controllerName = isset($url[0]) ? ucfirst($url[0]) . 'Controller' : $this->controller;
        }
        $controllerFile = $controllerDir . $controllerName . '.php';

        if (isset($url[0]) && file_exists($controllerFile)) {
            $this->controller = $controllerName;
            unset($url[0]);
            $url = array_values($url);
        } else {
            $controllerFile = $controllerDir . $this->controller . '.php';
        }

        if (file_exists($controllerFile)) {
            require_once $controllerFile;
            $this->controller = new $this->controller();
            if (str_starts_with($this->namespace, 'api')) {
                $httpMethod = $_SERVER['REQUEST_METHOD'];
                $id = isset($url[0]) ? $url[0] : null;

                if ($id !== null && is_numeric($id)) {
                    switch ($httpMethod) {
                        case 'GET': $this->method = 'show'; break;
                        case 'POST': 
                        case 'PUT':
                        case 'PATCH': $this->method = 'update'; break;
                        case 'DELETE': $this->method = 'destroy'; break;
                        default: $this->method = 'index';
                    }
                    $this->params = [(int)$id]; 
                    unset($url[0]);
                    $url = array_values($url);
                } else {
                    // Nếu URL KHÔNG có ID: /api/users
                    switch ($httpMethod) {
                        case 'GET': $this->method = 'index'; break;
                        case 'POST': $this->method = 'store'; break;
                        case 'DELETE': $this->method = 'destroy'; break; 
                        default: $this->method = 'index';
                    }
                    if (isset($url[0]) && !is_numeric($url[0]) && method_exists($this->controller, $url[0])) {
                        $this->method = $url[0];
                        unset($url[0]);
                        $url = array_values($url);
                    }
                    $this->params = []; 
                }

                if (!method_exists($this->controller, $this->method)) {
                    header('Content-Type: application/json');
                    http_response_code(405);
                    echo json_encode(['status' => 'error', 'message' => "Method {$httpMethod} Not Allowed on {$this->method}"]);
                    exit;
                }
            } else {
                if (isset($url[0]) && method_exists($this->controller, $url[0])) {
                    $this->method = $url[0];
                    unset($url[0]);
                    $url = array_values($url);
                }
                $this->params = $url ? array_values($url) : [];
            }
        } else {
            die("Controller file not found: " . $controllerFile);
        }

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
