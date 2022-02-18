<?php

declare(strict_types=1);

namespace Core;

use Core\DotEnv\DotEnv;
use Core\DotEnv\DotEnvRequirement;
use Psr\Container\ContainerInterface;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

/**
 * The main app controller, it takes care of the whole application initialization and life cycle
 */

class App
{
    /**
     * Our dependecy injector
     *
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var Http\HttpRouter
     */
    private $httpRouter;

    /**
     * @var DotEnv\DotEnv
     */
    private $dotEnv;

    /**
     * @var Doctrine\ORM\EntityManager
     */
    public $entityManager;

    /**
     * Constructor of our application
     * @param string $realPath the root path of the application
     * @param array Website Config Array
     */
    public function __construct(
        protected string $realPath,
        protected array $webConfig
    ) {
    }

    /**
     * @see \Core\DotEnv\DotEnv::init()
     */
    public static function dotEnvRequirements(): void
    {
        DotEnv::addRequirement("DEBUG_MODE", DotEnvRequirement::BOOLEAN);
        DotEnv::addRequirement("DATABASE_URL");
    }

    /**
     * Sets the container class for the application
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container): void
    {
        $this->container = $container;
    }

    /**
     * Gets a value from the dependecy injector container
     * This can be an object, a value, etc.
     *
     * @param mixed $key Class name
     * @return mixed value
     */
    public function get($key)
    {
        return $this->container->get($key);
    }

    /**
     * Returns application root path
     * @return string
     */
    public function getRealPath(): string
    {
        return $this->realPath;
    }

    /**
     * Returns a value from website config
     * @param string config entry key name
     * @return mixed
     */
    public function getConfig(string $key)
    {
        if (!isset($this->webConfig[$key])) {
            return null;
        }

        return $this->webConfig[$key];
    }

    /**
     * Initialize application and runs it
     * calls all classes initialize routines and dispatch routines
     */
    public function run(): void
    {
        $this->dotEnv = $this->get(DotEnv::class);
        $this->dotEnv->init();

        $entityConfig = Setup::createAnnotationMetadataConfiguration(
            [$this->realPath . '/app/Models'],
            boolval($this->dotEnv->get("DEBUG_MODE"))
        );

        $this->entityManager = EntityManager::create(
            ['url' => $this->dotEnv->get("DATABASE_URL")],
            $entityConfig
        );

        $this->httpRouter = $this->container->get(Http\HttpRouter::class);

        // Don't dispatch Http router on cli usage
        if (php_sapi_name() !== 'cli') {
            $this->httpRouter->run();
        }
    }
}
