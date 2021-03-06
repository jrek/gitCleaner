<?php
chdir(__DIR__);
$filePath = realpath(ltrim($_SERVER["REQUEST_URI"], '/'));
if ($filePath === false) {
    $filePath = realpath(ltrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH),'/'));
}

if ($filePath && is_dir($filePath)){
    // attempt to find an index file
    foreach (['index.php', 'index.html'] as $indexFile){
        if ($filePath = realpath($filePath . DIRECTORY_SEPARATOR . $indexFile)){
            break;
        }
    }
}
if ($filePath && is_file($filePath)) {
    // 1. check that file is not outside of this directory for security
    // 2. check for circular reference to router.php
    // 3. don't serve dotfiles
    if (strpos($filePath, __DIR__ . DIRECTORY_SEPARATOR) === 0 &&
        $filePath != __DIR__ . DIRECTORY_SEPARATOR . 'router.php' &&
        substr(basename($filePath), 0, 1) != '.'
    ) {
        $ext = pathinfo($filePath, PATHINFO_EXTENSION);
        if ($ext == 'php') {
            // php file; serve through interpreter
            include $filePath;
        } else {
            switch ($ext) {
                case 'css':
                    $type = 'text/css';
                    break;
                case 'js':
                    $type = 'text/javascript';
                default:
                    $type = 'text/html';
                    break;
            }
            header("Content-Type: " . $type);
            include $filePath;
        }
    } else {
        // disallowed file
        header("HTTP/1.1 404 Not Found");
        echo "404 Not Found";
    }
} else {
    // rewrite to our index file
    include __DIR__ . DIRECTORY_SEPARATOR . 'index.php';
}
