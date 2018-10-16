<?php

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use App\Controllers\IndexController;
use App\Controllers\SettingsController;

// Add Route object(s) to RouteCollection object
$routes = new RouteCollection();
$routes->add(IndexController::class.'::index', new Route(
    '/',
    ['controller' => IndexController::class, 'method'=>'indexAction']
));
$routes->add(IndexController::class.'::delete', new Route(
    '/index/delete',
    ['controller' => IndexController::class, 'method'=>'deleteAction']
));

$routes->add(SettingsController::class.'::index', new Route(
    '/settings',
    ['controller' => SettingsController::class, 'method'=>'indexAction']
));
$routes->add(SettingsController::class.'::save', new Route(
    '/settings/save',
    ['controller' => SettingsController::class, 'method'=>'saveAction']
));

