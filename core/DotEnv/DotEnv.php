<?php

declare(strict_types=1);

namespace Core\DotEnv;

/**
 * The .env settings handler, takes care of loading it and validating it.
 * its also responsible of returning values from .env
 */

class DotEnv
{
    /**
     * @var \Core\App
     */
    private $app;

    /**
     * Defined .env requirements
     * @var DotEnvRequirement[]
     */
    private static $requirements = [];

    /**
     * Initialize the DotEnv enviroment
     */
    public function __construct(\Core\App $app)
    {
        $this->app = $app;
        return;
    }

    /**
     * Adds a new DotEnv requirement
     */
    public static function addRequirement(
        string $name,
        int $type = DotEnvRequirement::STRING,
        bool $isRequired = true,
        bool $nonEmpty = true,
        array $allowedValues = []
    ): void {
        self::$requirements[] = new DotEnvRequirement($name, $type, $isRequired, $nonEmpty, $allowedValues);
    }

    /**
     * Returns .env variable
     * @param string $key the variable name
     * @return ?mixed returns the stored data or null if non-existance
     */
    public static function get(string $key)
    {
        if (isset($_ENV[$key])) {
            return $_ENV[$key];
        }

        return null;
    }

    /**
     * Initialize .env
     */
    public function init(): void
    {
        $requirementsCallbacks = $this->app->get('dotenv.config');

        foreach ($requirementsCallbacks as $callback) {
            $callback();
        }

        $dotenv = \Dotenv\Dotenv::createImmutable($this->app->getRealPath());
        $dotenv->load();

        foreach (self::$requirements as $requirement) {
            $field = $dotenv;

            if ($requirement->isRequired()) {
                $field = $field->required($requirement->getName());
            } else {
                $field = $field->ifPresent($requirement->getName());
            }

            switch ($requirement->getType()) {
                case DotEnvRequirement::BOOLEAN:
                    $field = $field->isBoolean();
                    break;
                case DotEnvRequirement::INTEGER:
                    $field = $field->isInteger();
                    break;
            }

            if ($requirement->isNonEmpty()) {
                $field = $field->notEmpty();
            }

            if (count($requirement->getAllowedValues()) > 0) {
                $field = $field->allowedValues($requirement->getAllowedValues());
            }
        }
    }
}
