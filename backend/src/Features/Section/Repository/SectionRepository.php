<?php

namespace App\Features\Section\Repository;

use App\Document\Section\Section;
use App\Helper\Enum\LocaleType;
use Doctrine\Bundle\MongoDBBundle\{ManagerRegistry, Repository\ServiceDocumentRepository};
use MongoDB\BSON\Regex;

class SectionRepository extends ServiceDocumentRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Section::class);
    }

    /** @return Section[] */
    public function findChildrenByRegex(array $regex, string $locale): array
    {
        return $this->createQueryBuilder()
            ->hydrate(false)
            ->select('code')
            ->field('locale')
            ->equals(LocaleType::fromString($locale)->value)
            ->field('active')
            ->equals(true)
            ->field('path')
            ->in($regex)
            ->getQuery()
            ->toArray();
    }

    public function findChildrenByFullPath(array $path, int $locale): array
    {
        return $this->findBy([
            'path' => new Regex(
                implode(',', $path) . '(,|$)'
            ),
            'locale' => $locale
        ]);
    }
}
