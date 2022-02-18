<?php

declare(strict_types=1);

namespace Core\Http\Resources;

use Core\Http\HttpResource;

/**
 * A default resource for 405 pages
 */

class Resource405 extends HttpResource
{
    /**
     * returns a default 405 error page
     * it also handles OPTIONS request
     * @param array $vars variables passed from the router
     * @return array error code, rendered template
     */
    public function any(array $vars): array
    {
        header("Allow: " . implode(', ', $vars['methods']));
        header("Access-Control-Allow-Methods:" . implode(', ', $vars['methods']));
        header("Access-Control-Allow-Headers: *");
        return [($vars['usedMethod'] == 'OPTIONS') ? 200 : 405, null];
    }
}
