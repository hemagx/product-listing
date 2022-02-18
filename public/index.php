<?php

/**
 * Entry point file for webserver, responsible of creating our app and serving response to user.
 */

/**
 * Special clause for php built-in web server to serve static files
 */
if (php_sapi_name() === 'cli-server') {
    if (substr_compare($_SERVER["REQUEST_URI"], "/static/", 0, strlen("/static/"), true) === 0) {
        return false;
    }
}

/**
 * Bootstrap the application
 */
$app = require_once __DIR__ . '/../bootstrap/bootstrap.php';

// And now let's run it!
$app->run();
