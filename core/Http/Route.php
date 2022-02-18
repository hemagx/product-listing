<?php

declare(strict_types=1);

namespace Core\Http;

/**
 * A class that represents a route and its options
 */

class Route
{
    /**
     * Constructs a route class
     *
     * @param string|array $methods the possible request methods, can be an upper case string method like eg. GET
     *              or an array of methods eg. ["GET", "POST"]
     * @param string $route route matching pattren, refer to https://github.com/nikic/FastRoute for full details
     * @param HttpResource|string $handler should be a callable
     */
    public function __construct(
        protected $methods,
        protected string $route,
        protected HttpResource|string $handler
    ) {
        if (!is_subclass_of($handler, HttpResource::class)) {
            throw new \InvalidArgumentException("Incorrect argument type expected HttpResource");
        }

        // Convert methods into array if necessary
        if (is_array($methods)) {
            $this->methods = $methods;
        } else {
            $this->methods = [$methods];
        }

        $this->route = $route;
        $this->handler = $handler;
    }

    /**
     * Returns the methods of this route
     * @return string[]
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * Returns the route pattren of this route
     *
     * @return string
     */
    public function getRoute(): string
    {
        return $this->route;
    }

    /**
     * Return the handler of this route
     * @return callable|HttpResource
     */
    public function getHandler()
    {
        return $this->handler;
    }
}
