<?php

declare(strict_types=1);

namespace App\Models;

use Doctrine\ORM\Mapping;

/**
 * @Entity
 */
class ProductFurniture extends Product
{
    /**
     * @Column(type="float")
     * @var float
     */
    private $height;

    /**
     * @Column(type="float")
     * @var float
     */
    private $width;

    /**
     * @Column(type="float")
     * @var float
     */
    private $length;


    /**
     * Gets product height
     * @return float
     */
    public function getHeight(): float
    {
        return $this->height;
    }

    /**
     * Sets product height
     * @param float $height
     * @return self
     */
    public function setHeight(float $height): self
    {
        $this->height = $height;
        return $this;
    }

    /**
     * Gets product width
     * @return float
     */
    public function getWidth(): float
    {
        return $this->width;
    }

    /**
     * Sets product width
     * @param float $width
     * @return self
     */
    public function setWidth(float $width): self
    {
        $this->width = $width;
        return $this;
    }

    /**
     * Gets product length
     * @return float
     */
    public function getLength(): float
    {
        return $this->length;
    }

    /**
     * Sets product length
     * @param float $length
     * @return self
     */
    public function setLength(float $length): self
    {
        $this->length = $length;
        return $this;
    }

    /**
     * Returns product description
     * @return array
     */
    public function getDescription(): array
    {
        return [
            'height' => $this->getHeight(),
            'width' => $this->getWidth(),
            'length' => $this->getLength(),
        ];
    }

    /**
     * Returns a product type name
     * @return string
     */
    public static function getProductTypeName(): string
    {
        return 'Furniture';
    }

    /**
     * Returns a form fields map for usage in html
     * @return array
     */
    public static function getFormMap(): array
    {
        return [
            'height' => (object)[
                'type' => 'number',
                'exclusiveMinimum' => true,
                'minimum' => 0,
                'maximum' => PHP_FLOAT_MAX,
            ],
            'width' => (object)[
                'type' => 'number',
                'exclusiveMinimum' => true,
                'minimum' => 0,
                'maximum' => PHP_FLOAT_MAX,
            ],
            'length' => (object)[
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
        $this->setHeight($form->height);
        $this->setWidth($form->width);
        $this->setLength($form->length);
    }
}
