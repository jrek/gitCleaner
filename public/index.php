<?php

Define('APP_PATH', dirname(__DIR__).'/app');

use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use App\Services\SharedService;

require_once dirname(__DIR__).'/vendor/autoload.php';
require_once APP_PATH.'/config/routes.php';

$service = new SharedService([
    'default' => APP_PATH.'/config/default.config.php',
    'config' => APP_PATH.'/config/config.php'
]);

try {
    // Init RequestContext object
    $context = new RequestContext();
    $context->fromRequest($service->get('request'));

    // Init UrlMatcher object
    $matcher = new UrlMatcher($routes, $context);
    // Find the current route
    $routeParams = $matcher->match($context->getPathInfo());

} catch (ResourceNotFoundException $e) {
    $routeParams = [];
    echo $e->getMessage();
}

if (!empty($routeParams)) {
    $controller = new $routeParams['controller'](
        $service,
        $service->get('request') ,
        $service->get('response') ,
        $service->get('Twig'),
        $service->get('ConfigHelper')
    );
    call_user_func([$controller, $routeParams['method']]);
    $controller->response();
} else {
    echo '404 not found';
}
