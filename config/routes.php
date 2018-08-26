<?php

/**
 * App routes in this file
 */

use App\Controller\AppController;

/******************************************************** APP ********************************************************/
$app->group('/', function() {
    $this
        ->map(['GET', 'OPTIONS'], '', AppController::class . ':index')
        ->setName('app.index')
    ;
});