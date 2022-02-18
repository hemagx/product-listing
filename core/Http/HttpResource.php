<?php

declare(strict_types=1);

namespace Core\Http;

use Core\DotEnv\DotEnv;
use Core\Http\HttpRouter;
use Core\App;
use Twig\Extra\Intl\IntlExtension;

/**
 * The Http resouce is reponsible for constructing a response to the client
 * It either renders a view and/or returning http response
 * the HttpRouter automatically calls a method matching the request method for example HttpResource::get
 * therefore each allowed request method shall have a defined class method with same name in lowerCASE
 * otherwise the HttpRouter returns a gateway error
 */

class HttpResource
{
    /**
     * @var \Twig\Environment
     */
    protected $twig;

    /**
     * @var string
     */
    public $title = null;

    /**
     * Constructs the required objects for a HTTP response
     * if you wish to override this make sure to call parent constructure, else things won't work.
     */
    public function __construct(
        protected App $app,
        protected HttpRouter $httpRouter,
        protected DotEnv $dotEnv
    ) {
        $twigLoader = new \Twig\Loader\FilesystemLoader($this->app->getRealPath() . '/app/Views');
        $this->twig = new \Twig\Environment($twigLoader, [
            'debug' => boolval($this->dotEnv->get('DEBUG_MODE')),
            'cache' => $this->app->getRealPath() . '/' . $this->dotEnv->get("TWIG_CACHE_PATH"),
        ]);

        // Install Twig extensions
        $this->twig->addExtension(new IntlExtension());

        // Expose classes to template engine
        $this->twig->addGlobal('app', $this->app);
        $this->twig->addGlobal('http', $this->httpRouter);
        $this->twig->addGlobal('self', $this);
    }

    /**
     * @see \Core\DotEnv\DotEnv::init()
     */
    public static function dotEnvRequirements(): void
    {
        DotEnv::addRequirement("TWIG_CACHE_PATH");
    }

    /**
     * An example response function to a get method request
     * This function defaults to error code 501 in-case it was never overriden
     * @param array $vars an array of defined variables in the route
     * @return array an array where first elements is the response code, and second element
     *               is either the rendred template string or an json object or null if nothing shall be returned
     */
    /*
    public function get(array $var): array
    {
        return [501, null];
    }
    */

    /**
     * An example response function to a catch all methods
     * @param array $vars an array of defined variables in the route
     * @return array an array where first elements is the response code, and second element
     *               is either the rendred template string or an json object or null if nothing shall be returned
     */
    /*
    public function any(array $var): array
    {
        return [501, null];
    }
    */
}
