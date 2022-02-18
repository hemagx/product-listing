<?php

declare(strict_types=1);

namespace Core\Http;

use Core\DotEnv\DotEnv;
use Core\App;
use Core\Http\Resources\Resource404;
use Core\Http\Resources\Resource405;
use FastRoute\RouteCollector;
use FastRoute\Dispatcher;

/**
 * The routing controller
 * it takes care of routing requests to resources and doing sanity checks on requests if any
 */

class HttpRouter
{
    /**
     * An array of available routes
     *
     * @var Route[]
     */
    private static $routes = [];

    /**
     * @var \FastRoute\Dispatcher
     */
    private $dispatcher;

    /**
     * The default dispatcher of error pages
     *
     * @var HttpResource[]
     */
    private static $errorDispatcher = [
        404 => Resource404::class,
        405 => Resource405::class,
    ];

    /**
     * Initialize the Http router and loads routes
     */
    public function __construct(
        protected App $app,
        protected DotEnv $dotEnv)
    {
        require_once $this->app->getRealPath() . '/routes/routes.php';
    }

    /**
     * @see \Core\DotEnv\DotEnv::init()
     */
    public static function dotEnvRequirements(): void
    {
        DotEnv::addRequirement("ROUTE_CACHE_PATH");
    }

    /**
     * Registers a new route
     *
     * @param string|array $methods the possible request methods, can be an upper case string method like eg. GET
     *              or an array of methods eg. ["GET", "POST"]
     * @param string $route route matching pattren, refer to https://github.com/nikic/FastRoute for full details
     * @param HttpResource $handler
     */
    public static function addRoute($methods, string $route, $handler): void
    {
        self::$routes[] = new Route($methods, $route, $handler);
    }

    /**
     * Register a error code page overrider
     *
     * @param int $errorCode the error code page to be overriden
     * @param HttpResource $handler
     */
    public static function overrideError(int $errorCode, $handler): void
    {
        if (!isset(self::$errorDispatcher[$errorCode])) {
            $allowedErrors = array_keys(self::$errorDispatcher);
            throw new \InvalidArgumentException("Incorrect argument type expected a value in [" . implode(", ", $allowedErrors) . "]");
        }

        if (!is_subclass_of($handler, HttpResource::class)) {
            throw new \InvalidArgumentException("Incorrect argument type expected HttpResource");
        }

        self::$errorDispatcher[$errorCode] = $handler;
    }

    /**
     * Registers route into FastRoute dispatcher
     * @param RouteCollector $dispatcher
     */
    public static function registerDispatcher(RouteCollector $dispatcher): void
    {
        foreach (self::$routes as $route) {
            $dispatcher->addRoute($route->getMethods(), $route->getRoute(), $route->getHandler());
        }
    }

    /**
     * Runs the HTTP Dispatcher and executes route
     */
    public function run(): void
    {
        $settings = [
            'cacheFile' => $this->app->getRealPath() . '/' . $this->dotEnv->get('ROUTE_CACHE_PATH'),
            'cacheDisabled' => $this->dotEnv->get('DEBUG_MODE'),
        ];

        $this->dispatcher = \FastRoute\cachedDispatcher(function (RouteCollector $r) {
            HttpRouter::registerDispatcher($r);
        }, $settings);

        $uri = $_SERVER['REQUEST_URI'];
        // Strip query string (?foo=bar) and decode URI
        if (($pos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $pos);
        }
        $uri = rawurldecode($uri);

        $routeInfo = $this->dispatcher->dispatch($_SERVER['REQUEST_METHOD'], $uri);
        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                $this->response(self::$errorDispatcher[404], []);
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                $this->response(
                    self::$errorDispatcher[405],
                    ['methods' => $allowedMethods, 'usedMethod' => $_SERVER['REQUEST_METHOD']]
                );
                break;
            case Dispatcher::FOUND:
                $this->response($routeInfo[1], $routeInfo[2]);
                break;
        }
    }

    /**
     * Sends a response back to the client after calling a handler
     * In-case if the handler doesn't have the method enabled it returns error 501
     * @var string $handler the handler class name
     * @var array $vars the variables passed down from the router
     */
    private function response(string $handler, array $vars): void
    {
        $method = strtolower($_SERVER['REQUEST_METHOD']);

        $response = $this->app->get($handler);

        $callMethod = null;

        if (method_exists($response, $method)) {
            $callMethod = $method;
        } elseif (method_exists($response, 'any')) {
            $callMethod = 'any';
        }

        if ($callMethod === null) {
            http_response_code(501);
            return;
        }

        $view = $response->$callMethod($vars);
        http_response_code($view[0]);
        header('Access-Control-Allow-Origin: *');

        if ($view !== null) {
            if (is_string($view[1])) { // Normal rendred template
                print($view[1]);
            } elseif (is_array($view[1])) { // JSON response
                header('Content-Type: application/json');
                print(json_encode($view[1]));
            }
        }
    }
}
