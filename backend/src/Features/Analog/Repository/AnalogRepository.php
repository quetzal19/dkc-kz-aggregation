<?php

namespace App\Features\Analog\Repository;

use App\Document\Analog\Analog;
use App\Helper\Abstract\AbstractSectionServiceDocumentRepository;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;

class AnalogRepository extends AbstractSectionServiceDocumentRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Analog::class);
    }

    public function getActiveAnalogs(string $productCode, ?string $sectionName, string $locale): array
    {
        return $this->getActiveProductCodesByProperty($productCode, $sectionName, $locale, 'analog');
    }
}