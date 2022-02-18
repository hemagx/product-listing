<?php

declare(strict_types=1);

namespace Core\Http\Resources;

use Core\Http\HttpResource;

/**
 * A default resource for 404 pages
 */

class Resource404 extends HttpResource
{
    /**
     * returns a default 404 error page
     * @param array $vars variables passed from the router
     * @return array error code, rendered template
     */
    public function get(array $vars): array
    {
        return [404, 'Opps doesn\'t exists :('];
    }
}
