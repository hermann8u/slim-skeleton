<?php

/**
 * Permanent App Middleware in this file. The first declared, the last called.
 */

use Slim\Http\Request;
use Slim\Http\Response;
use Tuupola\Middleware\JwtAuthentication;

$app->add(new JwtAuthentication($app->getContainer()->get('settings')['jwt']));

/**
 * Set some header like CORS and HTTP methods
 */
$app->add(function(Request $request, Response $response, callable $next) {
    return $next($request, $response)
        ->withHeader('Access-Control-Allow-Origin', $this->get('settings')['cors'])
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
    ;
});

/**
 * Permanently redirect paths with a trailing slash to their non-trailing counterpart
 */
$app->add(function(Request $request, Response $response, callable $next) {
    $uri = $request->getUri();
    $path = $uri->getPath();
    if ($path != '/' && substr($path, -1) == '/') {
        $uri = $uri->withPath(substr($path, 0, -1));

        if($request->getMethod() == 'GET') {
            return $response->withRedirect((string)$uri, 301);
        }
        else {
            return $next($request->withUri($uri), $response);
        }
    }

    return $next($request, $response);
});