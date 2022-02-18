<?php

declare(strict_types=1);

namespace App\Models;

use Doctrine\ORM\Mapping;

/**
 * @Entity
 */
class ProductBook extends Product
{
    /**
     * @Column(type="float")
     * @var float
     */
    private $weight;

    /**
     * Gets book weight
     * @return float
     */
    public function getWeight(): float
    {
        return $this->weight;
    }

    /**
     * Sets a book weight
     * @param float $weight book weight in KG
     * @return self
     */
    public function setWeight(float $weight): self
    {
        $this->weight = $weight;
        return $this;
    }

    /**
     * Returns product description
     * @return array
     */
    public function getDescription(): array
    {
        return [
            'weight' => $this->getWeight(),
            'unit' => 'kg',
        ];
    }

    /**
     * Returns a product type name
     * @return string
     */
    public static function getProductTypeName(): string
    {
        return 'Book';
    }

    /**
     * Returns a form fields map for usage in html
     * @return array
     */
    public static function getFormMap(): array
    {
        return [
            'weight' => (object)[
                'type' => 'number',
                'exclusiveMinimum' => true,
                'minimum' => 0,
                'maximum' => PHP_FLOAT_MAX,
            ],
        ];
    }

    /**
     * Sets product specific fields from a json object
     * @param object form
     */
    public function setFormFields(object $form): void
    {
        $this->setWeight($form->weight);
    }
}
