<?php

/**
 * The config file of our application
 * it constructs our Dependecy injector container and returns it back
 */

use function DI\factory;
use Psr\Container\ContainerInterface;

/**
 * Defines website config
 */
$web_config = [
    'siteTitle' => 'Scandiweb Junior Task', // Website title
    'storeCurrency' => 'USD', // Store currency, takes 3 letters ISO format
];

/**
 * Defines callables that may define requirements for the .env file
 */

$dotenv_config = [
    "\Core\App::dotEnvRequirements",
    "\Core\Http\HttpRouter::dotEnvRequirements",
    "\Core\Http\HttpResource::dotEnvRequirements",
];

return [
    'website.config' => $web_config,
    'dotenv.config' => $dotenv_config,
    \Core\App::class => function (ContainerInterface $c) {
        return new \Core\App(realpath(__DIR__ . '/..'), $c->get('website.config'));
    },
];
