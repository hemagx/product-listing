<?php

declare(strict_types=1);

namespace App\Models;

use Doctrine\ORM\EntityRepository;

/**
 * Products Repository
 */
class ProductRepository extends EntityRepository
{
    /**
     * Overrides findAll to order it asc by id
     */
    public function findAll()
    {
        return $this->findBy([], ['id' => 'ASC']);
    }
}
