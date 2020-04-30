<?php
use Cake\Routing\RouteBuilder;
use Cake\Routing\Route\InflectedRoute;

Cake\Routing\Router::plugin('VatNumberCheck', ['path' => '/vat_number_check'], function (RouteBuilder $routes) {
    $routes->addExtensions(['json']);
    $routes->fallbacks(InflectedRoute::class);
});
