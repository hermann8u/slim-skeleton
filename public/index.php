<?php

if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

defined('ROOT') ?: define('ROOT', dirname(__DIR__) . DIRECTORY_SEPARATOR);

$loader = require ROOT . 'vendor/autoload.php';

// Load .env file
if (file_exists(ROOT . '.env')) {
    $dotenv = new Symfony\Component\Dotenv\Dotenv();
    $dotenv->load(ROOT . '.env');
}
else {
    echo "No \".env\" file found at the project root !";
    die();
}

Doctrine\Common\Annotations\AnnotationRegistry::registerLoader([$loader, 'loadClass']);

$settings = require ROOT . 'config/settings.php';

$app = new \Slim\App($settings);
$container = $app->getContainer();

require ROOT . 'config/services.php';
require ROOT . 'config/middleware.php';
require ROOT . 'config/routes.php';

$app->run();
