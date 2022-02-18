<?php

declare(strict_types=1);

namespace App\Resources;

use JsonSchema\Validator;

/**
 * The class responsible of all the logic related to showing product list page and mass deletion of products
 */

class Product extends \Core\Http\HttpResource
{
    /**
     * Returns a json response with product list
     * @param array $vars variables passed from the router
     * @return array error code, json response
     */
    public function get(array $vars): array
    {
        $products = $this->app->entityManager->getRepository('App\Models\Product')->findAll();
        $response = [];

        foreach ($products as $product) {
            $response[$product->getId()] = [
                'type' => $product->getProductTypeName(),
                'sku' => $product->getSku(),
                'name' => $product->getName(),
                'price' => $product->getPrice(),
                'description' => $product->getDescription(),
            ];
        }

        return [200, $response];
    }

    /**
     * Deletes a list of products
     * @param array $vars variables passed from the router
     * @return array error code, json response
     */
    public function delete(array $vars): array
    {
        $requestBody = json_decode(file_get_contents('php://input'));
        $deleteIds = [];
        foreach ($requestBody as $productId => $delete) {
            if (!$delete) {
                continue;
            }

            $deleteIds[] = $productId;
        }

        if (count($deleteIds) > 0) {
            $qb = $this->app->entityManager->createQueryBuilder();
            $qb->delete('App\Models\Product', 'P')
            ->where('P.id IN (:ids)')
            ->setParameter('ids', $deleteIds);
            $qb->getQuery()->execute();
        }

        return [200, $deleteIds];
    }

    /**
     * Returns a specific product form fields in object format for json schema
     * validation
     * @param string productType product type name
     * @return mixed an array of fields objects or null if none
     */
    private function getProductFormMap(string $productType)
    {
        $productClasses = \App\Models\Product::getTypeNames();

        if (!isset($productClasses[$productType])) {
            return null;
        }

        return $productClasses[$productType]::getFormMap();
    }

    /**
     * Returns an array of product type names
     * @return string[]
     */
    private function getProductTypeNames(): array
    {
        return array_keys(\App\Models\Product::getTypeNames());
    }

    /**
     * Adds a new product
     * @param array $vars variables passed from the router
     * @return array error code, json response
     */
    public function post(array $vars): array
    {
        $requestBody = json_decode(file_get_contents('php://input'));
        $validationSchema = (object)[
            'type' => 'object',
            'properties' => [
                'sku' => (object)[
                    'type' => 'string',
                    'minLength' => 1,
                    'maxLength' => 255,
                ],
                'name' => (object)[
                    'type' => 'string',
                    'minLength' => 1,
                    'maxLength' => 255,
                ],
                'productType' => (object)[
                    'type' => 'string',
                    'enum' => $this->getProductTypeNames(),
                ],
                'price' => (object)[
                    'type' => 'number',
                    'minimum' => 0,
                    'maximum' => PHP_FLOAT_MAX,
                ],
            ],
            'required' => ['sku', 'name', 'productType', 'price'],
        ];

        if (isset($requestBody->productType) && $this->getProductFormMap($requestBody->productType) !== null) {
            $validationSchema->properties = array_merge(
                $validationSchema->properties,
                $this->getProductFormMap($requestBody->productType)
            );
            $validationSchema->required = array_merge(
                $validationSchema->required,
                array_keys($this->getProductFormMap($requestBody->productType))
            );
        }

        $validationSchema->properties = (object)$validationSchema->properties;

        $validator = new Validator();
        $validator->validate(
            $requestBody,
            $validationSchema,
        );
        if (!$validator->isValid()) {
            $errors = [];

            foreach ($validator->getErrors() as $error) {
                $errors[$error['property']] = $error['message'];
            }

            return [400, $errors];
        }

        if (
            $this->app->entityManager->getRepository('App\Models\Product')->findOneBy(
                ['sku' => $requestBody->sku]
            ) !== null
        ) {
            return [400, ['sku' => 'SKU already exists']];
        }

        $productClass = \App\Models\Product::getTypeNames()[$requestBody->productType];
        $product = new $productClass();
        $product->setSku($requestBody->sku);
        $product->setName($requestBody->name);
        $product->setPrice($requestBody->price);
        $product->setFormFields($requestBody);

        $this->app->entityManager->persist($product);
        $this->app->entityManager->flush();

        return [200, []];
    }
}
