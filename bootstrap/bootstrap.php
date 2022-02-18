<?php

/**
 * The bootstraping file
 * Initialize the application and returns it back
 */

/**
 * Register the composer auto loader
 */
require_once __DIR__ . '/../vendor/autoload.php';

use DI\ContainerBuilder;

/**
 * Build our dependecy injector
 */
$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(__DIR__ . '/config.php');
$container = $containerBuilder->build();

/**
 * Register the container into our application
 */
$app = $container->get(\Core\App::class);
$app->setContainer($container);

return $app;
