<?php

namespace App\Features\Accessory\Repository;

use App\Document\Accessory\Accessory;
use App\Helper\Abstract\AbstractSectionServiceDocumentRepository;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;

class AccessoryRepository extends AbstractSectionServiceDocumentRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Accessory::class);
    }

    public function getActiveAccessories(string $productCode, ?string $sectionName, string $locale): array
    {
        return $this->getActiveProductCodesByProperty($productCode, $sectionName, $locale, 'accessory');
    }

}