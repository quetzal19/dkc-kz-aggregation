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

    public function getActiveAccessories(string $productId, ?string $sectionName, string $locale): array
    {
        return $this->getActiveProductCodesByProperty($productId, $sectionName, $locale, 'accessory');
    }

    public function getAccessoriesSections(string $productId, string $locale): array
    {
        return parent::getSections($productId, $locale, 'accessory');
    }
}