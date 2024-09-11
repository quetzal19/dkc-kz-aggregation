<?php

namespace App\Features\Property\Repository;

use App\Document\Properties\Property;
use Doctrine\Bundle\MongoDBBundle\{ManagerRegistry, Repository\ServiceDocumentRepository};

class PropertyRepository extends ServiceDocumentRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Property::class);
    }
}