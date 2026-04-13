<?php

class Controller
{
    public function model(string $model): object
    {
        require_once APPROOT . '/models/' . $model . '.php';
        return new $model();
    }

    public function view(string $view, array $data = []): void
    {
        if (file_exists(APPROOT . '/views/' . $view . '.php')) {
            extract($data);
            require_once APPROOT . '/views/' . $view . '.php';
        } else {
            die('View ' . $view . ' does not exist.');
        }
    }
}
