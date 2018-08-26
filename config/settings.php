<?php

/**
 * App settings
 */

return [
    'settings' => [
        'displayErrorDetails' => getenv('APP_ENV') !== 'prod', // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header
        'cors' => getenv('CORS_ALLOWED_ORIGINS') ?: '*',
        // Enable caching for routes in production only
        'routerCacheFile' => getenv('APP_ENV') === 'prod' ? ROOT . 'var/cache/routes.php' : false,

        // App settings
        // Define your custom settings here to get them from the controller shortcut method
        'app' => [
            'name' => getenv('APP_NAME'),
            'url' => getenv('APP_URL'),
            'env' => getenv('APP_ENV'),
            'root_path' => ROOT,
            'public_path' => ROOT . 'public/'
        ],

        'jwt'  => [
            'secret' => getenv('JWT_SECRET'),
            'secure' => false, // true for https only
            'path' => ["/"],
            'ignore' => ["/security/login$", "/security/register$", "/security/facebook-login$", "/$"],
            'token_life_time' => '2 hours'
        ],
        // Database settings
        'database' => [
            'driver' => getenv('DB_CONNECTION') ?: 'pdo_mysql',
            'host' => getenv('DB_HOST') ?: 'localhost',
            'dbname' => getenv('DB_DATABASE') ?: 'slim-setup',
            'user' => getenv('DB_USERNAME') ?: 'root',
            'password' => getenv('DB_PASSWORD') ?: '',
            'port' => getenv('DB_PORT') ?: 3306,
            'charset' => 'UTF8'
        ],

        // Twig settings
        'twig' => [
            'template_dir' => ROOT. 'templates',
            'cache_dir' => ROOT . 'var/cache/twig'
        ],

        // Monolog settings
        'logger' => [
            'name' => getenv('APP_NAME'),
            'path' => isset($_ENV['docker']) ? 'php://stdout' : ROOT . 'var/logs/'.getenv('APP_ENV').'.log',
            'level' => getenv('APP_DEBUG') === 'true' ? \Monolog\Logger::DEBUG : \Monolog\Logger::INFO,
        ]
    ]
];
