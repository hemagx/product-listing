<?php

/**
 * A class that defines a requirement for a specific field in .env
 */

declare(strict_types=1);

namespace Core\DotEnv;

class DotEnvRequirement
{
    public const INTEGER = 0;
    public const BOOLEAN = 1;
    public const STRING = 2;

    /**
     * .env variable name
     * @var string
     */
    private $name;

    /**
     * Sets a variable type
     * Check class constants
     * @var int
     */
    private $type;

    /**
     * Sets a required variable
     * @var bool
     */
    private $isRequired;

    /**
     * Sets a non-empty variable
     * @var bool
     */
    private $nonEmpty;

    /**
     * Sets a set of allowed values
     * @var mixed[]
     */
    private $allowedValues;

    /**
     * Constructs a dot env variable requirements
     * @param string $name the variable name
     * @param int $type variable type (check class constants), defaults to STRING
     * @param bool $isRequired sets a variable as either required or not, defaults to true
     * @param bool $nonEmpty sets a variable to disallow empty values, defautls to true
     * @param array $allowedValues an array of allowed values, defaults to empty array (no check)
     */
    public function __construct(
        string $name,
        int $type = self::STRING,
        bool $isRequired = true,
        bool $nonEmpty = true,
        array $allowedValues = []
    ) {
        if (!$this->validateType($type)) {
            throw new \InvalidArgumentException("Incorrect argument type expected constant of DotEnvRequirement");
        }

        $this->name = $name;
        $this->type = $type;
        $this->isRequired = $isRequired;
        $this->nonEmpty = $nonEmpty;
        $this->allowedValues = $allowedValues;
    }

    /**
     * Validates that a type equals one of our constants
     * @param int $type the type
     * @return bool
     */
    private function validateType(int $type): bool
    {
        $reflect = new \ReflectionClass($this);
        $constants = $reflect->getConstants();

        foreach ($constants as $constant) {
            if ($type === $constant) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns variable name
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns variable type
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * Returns if a variable is required
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->isRequired;
    }

    /**
     * Returns if a variable should be non-empty
     * @return bool
     */
    public function isNonEmpty(): bool
    {
        return $this->nonEmpty;
    }

    /**
     * Returns allowed values
     * @return array
     */
    public function getAllowedValues(): array
    {
        return $this->allowedValues;
    }
}
