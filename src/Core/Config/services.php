<?php

/**
 * Define services
 *
 * @see /config/services.php
 */

// Database Connection
$container['database'] = function($c) {
    try {
        return Doctrine\DBAL\DriverManager::getConnection($c->get('settings')['database'], new Doctrine\DBAL\Configuration());
    }
    catch(\Doctrine\DBAL\DBALException $e) {
        echo $e->getMessage();
        die();
    }
};

// Monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));

    return $logger;
};

$container['foundHandler'] = function() {
    return new \Slim\Handlers\Strategies\RequestResponseArgs();
};

$container['manager'] = function($c) {
    return new \Core\Database\Manager($c->database);
};

$container['auth'] = function($c) {
    return new \Core\Security\Auth($c->manager, $c->settings['app']['url'], $c->settings['jwt']['secret'], $c->settings['jwt']['token_life_time']);
};

$container['validator'] = function($c) {
    $builder = \Symfony\Component\Validator\Validation::createValidatorBuilder();
    $builder
        ->enableAnnotationMapping()
        ->setConstraintValidatorFactory(new \Symfony\Component\Validator\ContainerConstraintValidatorFactory($c))
    ;

    if ('dev' !== $env = $c->settings['app']['env']) {
        $builder->setMetadataCache(new \Core\Cache\ValidatorCache($c->settings['app']['root_path'], $env));
    }

    return $builder->getValidator();
};

$container['validator.unique_value'] = function ($c) {
    return new \Core\Validator\Constraints\UniqueValueValidator($c->manager);
};

$container['validator.user_password'] = function ($c) {
    return new \Core\Validator\Constraints\UserPasswordValidator($c->request, $c->auth);
};

$container['view'] = function ($c) {
    $settings = $c->get('settings');
    $envProd = $settings['app']['env'] === 'prod';

    $view = new \Slim\Views\Twig($settings['twig']['template_dir'], [
        'cache' => $settings['twig']['cache_dir'],
        'debug' => !$envProd,
        'auto_reload' => !$envProd,
        'strict_variables' => true,
    ]);

    // Instantiate and add Slim specific extension
    $basePath = rtrim(str_ireplace('index.php', '', $c['request']->getUri()->getBasePath()), '/');
    $view->addExtension(new Slim\Views\TwigExtension($c['router'], $basePath));

    return $view;
};

$container['json_response_formatter'] = function ($c) {
    return new \Core\JsonResponseFormatter();
};