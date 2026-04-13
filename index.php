<?php
session_start();
require_once 'config/config.php';

spl_autoload_register(function (string $className): void {
    if (file_exists(APPROOT . '/core/' . $className . '.php')) {
        require_once APPROOT . '/core/' . $className . '.php';
    }
});

$app = new App();
