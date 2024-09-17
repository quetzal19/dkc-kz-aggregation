<?php

namespace App\Features\Section\Repository;

use App\Document\Section\Section;
use Doctrine\Bundle\MongoDBBundle\{ManagerRegistry, Repository\ServiceDocumentRepository};
use MongoDB\BSON\Regex;

class SectionRepository extends ServiceDocumentRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Section::class);
    }

    public function findChildrenByCode(array $path, int $locale): array
    {
        return $this->findBy([
            'path' => new Regex(
                implode(',', $path) . '(,|$)'
            ),
            'locale' => $locale
        ]);
    }
}
