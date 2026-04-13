<?php

function replaceInFile($filename, $replacements) {
    $content = file_get_contents($filename);
    $newContent = str_replace(array_keys($replacements), array_values($replacements), $content);
    if ($content !== $newContent) {
        file_put_contents($filename, $newContent);
        echo "Updated: $filename\n";
    }
}

$siteReplacements = [
    "\$_SESSION['user_login']" => "\$_SESSION['client_logged_in']",
    "\$_SESSION['user_id']" => "\$_SESSION['client_id']",
    "\$_SESSION['user_username']" => "\$_SESSION['client_username']",
    "\$_SESSION['user_hoten']" => "\$_SESSION['client_hoten']",
    "\$_SESSION['user_role']" => "\$_SESSION['client_role']",
    "\$_SESSION['user_avatar']" => "\$_SESSION['client_avatar']",
];

$adminReplacements = [
    "\$_SESSION['admin_login']" => "\$_SESSION['admin_logged_in']",
    "\$_SESSION['user_role']" => "\$_SESSION['admin_role']",
    "\$_SESSION['user_id']" => "\$_SESSION['admin_id']",
    "\$_SESSION['user_avatar']" => "\$_SESSION['admin_avatar']",
    "\$_SESSION['user_hoten']" => "\$_SESSION['admin_hoten']",
    "\$_SESSION['user_username']" => "\$_SESSION['admin_username']",
];

$appDir = __DIR__ . '/app';

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($appDir));

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $path = $file->getPathname();
        
        if (strpos(str_replace('\\', '/', $path), '/admin/') !== false) {
            replaceInFile($path, $adminReplacements);
        } else if (strpos(str_replace('\\', '/', $path), '/site/') !== false) {
            replaceInFile($path, $siteReplacements);
        }
    }
}

echo "Done.\n";
