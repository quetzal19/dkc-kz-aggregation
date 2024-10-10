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

    public function findActiveSection(string $sectionCode, string $locale): ?Section
    {
        $localeInt = LocaleType::fromString($locale)->value;

        $builder = $this->createAggregationBuilder();

        $builder
            ->match()
            ->field('code')
            ->equals($sectionCode)
            ->field('locale')
            ->equals($localeInt)
            ->addFields()
            ->field('fullPath')
            ->expression([
                [
                    '$split' => [
                        [
                            '$cond' => [
                                ['$ifNull' => ['$path', false]],
                                ['$concat' => ['$path', ',', '$code']],
                                '$code'
                            ]
                        ],
                        ','
                    ]
                ]
            ])
            ->unwind('$fullPath')
            ->lookup(Section::class)
            ->let(['fullPath' => '$fullPath'])
            ->pipeline([
                [
                    '$match' => [
                        '$expr' => [
                            '$in' => ['$code', '$$fullPath'],
                        ],
                    ]
                ],
                [
                    '$match' => [
                        'active' => false,
                        'locale' => $localeInt
                    ]
                ]
            ])
            ->alias('parentSection')
            ->match()
            ->field('parentSection')
            ->equals([]);

        $sections = $builder->hydrate(Section::class)->getAggregation()->getIterator()->toArray();
        return array_shift($sections);
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
            'active' => true,
            'locale' => $locale
        ]);
    }
}
