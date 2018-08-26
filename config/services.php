<?php

/**
 * Defines app services in this file and add them to the container.
 */

include ROOT . 'src/Core/Config/services.php';

$container['app.manager.user'] = function ($c) {
    return new \App\Manager\UserManager($c->manager, $c->validator);
};