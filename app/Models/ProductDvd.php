<?php

declare(strict_types=1);

namespace App\Models;

use Doctrine\ORM\Mapping;

/**
 * @Entity
 */
class ProductDvd extends Product
{
    /**
     * @Column(type="integer")
     * @var int
     */
    private $size;

    /**
     * Gets DVD size
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * Sets a DVD size
     * @param int $size DVD size in MB
     * @return self
     */
    public function setSize(int $size): self
    {
        $this->size = $size;
        return $this;
    }

    /**
     * Returns product description
     * @return array
     */
    public function getDescription(): array
    {
        return [
            'size' => $this->getSize(),
            'unit' => 'MB',
        ];
    }

    /**
     * Returns a product type name
     * @return string
     */
    public static function getProductTypeName(): string
    {
        return 'DVD';
    }

    /**
     * Returns a form fields map for usage in html
     * @return array
     */
    public static function getFormMap(): array
    {
        return [
            'size' => (object)[
                'type' => 'integer',
                'minimum' => 1,
                'maximum' => 2147483647,
            ],
        ];
    }

    /**
     * Sets product specific fields from a json object
     * @param object form
     */
    public function setFormFields(object $form): void
    {
        $this->setSize($form->size);
    }
}
