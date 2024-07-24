<?php

namespace App\Repository;

use App\Document\Product;
use App\Enum\SortOrder;
use App\Enum\SortType;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Bundle\MongoDBBundle\Repository\ServiceDocumentRepository;
use Doctrine\ODM\MongoDB\MongoDBException;

/**
 * Class ProductRepository
 *
 * @package App\Repository
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceDocumentRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @throws MongoDBException
     */
    public function findBySectionAndFilters(
        string $sectionCode,
        array $filters,
        SortType $sortType,
        SortOrder $sortOrder,
        int $page,
        int $ipp
    ): array {

        $query = $this->createQueryBuilder()
            ->field('sectionCode')->equals($sectionCode)
            ->field('active')->equals(true);

        if ($sortType === SortType::CODE) {
            $query->sort('code', $sortOrder->getIntValue());
        } elseif ($sortType === SortType::NAME) {
            $query->sort('name', $sortOrder->getIntValue());
        } elseif ($sortType === SortType::WEIGHT) {
            $query->sort('weight', $sortOrder->getIntValue());
        } elseif ($sortType === SortType::VOLUME) {
            $query->sort('volume', $sortOrder->getIntValue());
        }

        $query
            ->skip(($page - 1) * $ipp)
            ->limit($ipp);

        return $query->getQuery()->execute();
    }
}
