<?php

namespace App\Features\Properties\Property\DTO\Http\Response;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

final class PropertyFilterResponseDTO
{
    public function __construct(
        #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: PropertyFilterItemResponseDTO::class)))]
        /** @var PropertyFilterItemResponseDTO[] $filters */
        private array $filters,
        private int $count,
    ) {
    }

    public function getFilters(): array
    {
        return $this->filters;
    }

    public function setFilters(array $filters): PropertyFilterResponseDTO
    {
        $this->filters = $filters;
        return $this;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function setCount(int $count): PropertyFilterResponseDTO
    {
        $this->count = $count;
        return $this;
    }


}