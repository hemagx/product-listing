<?php

declare(strict_types=1);

namespace App\Models;

use Doctrine\DBAL\Types\IntegerType;
use Doctrine\ORM\Mapping;

/**
 * @Entity(repositoryClass="App\Models\ProductRepository")
 * @Table(name="product", indexes={@Index(name="type", columns={"type"})})
 * @InheritanceType("SINGLE_TABLE")
 * @DiscriminatorColumn(name="type", type="integer")
 * @DiscriminatorMap({
 *     0="ProductDvd",
 *     1="ProductBook",
 *     2="ProductFurniture",
 * })
 */
abstract class Product
{
    public const TYPE_DVD = 0;
    public const TYPE_BOOK = 1;
    public const TYPE_FURNITURE = 2;

    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     * @var int
     */
    private $id;

    /**
     * @Column(length=255, unique="TRUE")
     * @var string
     */
    private $sku;

    /**
     * @Column(length=255)
     * @var string
     */
    private $name;

    /**
     * @Column(type="float")
     * @var float
     */
    private $price;

    /**
     * gets product id
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }


    /**
     * sets product sku
     * @param string $sku sku value
     * @return self
     */
    public function setSku(string $sku): self
    {
        $this->sku = $sku;
        return $this;
    }

    /**
     * get product sku
     * @return string
     */
    public function getSku(): string
    {
        return $this->sku;
    }

    /**
     * sets product name
     * @param string $name product name
     * @return self
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * gets product name
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * sets product price
     * @param float $price
     * @return self
     */
    public function setPrice(float $price): self
    {
        $this->price = $price;
        return $this;
    }

    /**
     * gets product price
     * the product
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * gets product types ID and crosppoding class
     * @return array
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_DVD => ProductDvd::class,
            self::TYPE_BOOK => ProductBook::class,
            self::TYPE_FURNITURE => ProductFurniture::class,
        ];
    }

    /**
     * gets product type name and crospodding class
     * @return array
     */
    public static function getTypeNames(): array
    {
        return [
            'DVD' => ProductDvd::class,
            'Book' => ProductBook::class,
            'Furniture' => ProductFurniture::class,
        ];
    }

    /**
     * Returns a product type name
     * @return string
     */
    abstract public static function getProductTypeName(): string;

    /**
     * Returns a product description
     * @return array
     */
    abstract public function getDescription(): array;

    /**
     * Returns a form fields map for usage in html
     * @return array
     */
    abstract public static function getFormMap(): array;

    /**
     * Sets product specific fields from a json object
     * @param object form
     */
    abstract public function setFormFields(object $form): void;
}
