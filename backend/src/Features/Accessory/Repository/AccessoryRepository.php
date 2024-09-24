<?php

namespace App\Features\Accessory\Repository;

use App\Document\Accessory\Accessory;
use Doctrine\Bundle\MongoDBBundle\{ManagerRegistry, Repository\ServiceDocumentRepository};

class AccessoryRepository extends ServiceDocumentRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Accessory::class);
    }
}