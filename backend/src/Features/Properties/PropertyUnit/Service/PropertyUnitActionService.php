<?php

namespace App\Features\Properties\PropertyUnit\Service;

use App\Helper\Interface\{ActionInterface, Message\MessageDTOInterface};
use App\Document\Properties\{Name\PropertyName, Property};
use App\Features\Properties\Property\Repository\PropertyRepository;
use App\Features\Properties\PropertyName\DTO\Message\PropertyNameMessageDTO;
use App\Features\Properties\PropertyUnit\DTO\Message\PropertyUnitMessageDTO;
use Doctrine\Common\Collections\{ArrayCollection, Collection};
use Doctrine\ODM\MongoDB\DocumentManager;
use Psr\Log\LoggerInterface;

final readonly class PropertyUnitActionService implements ActionInterface
{
    public function __construct(
        private PropertyRepository $propertyRepository,
        private DocumentManager $documentManager,
        private LoggerInterface $logger,
    ) {
    }

    public function create(MessageDTOInterface $dto): bool
    {
        /** @var PropertyUnitMessageDTO $dto */
        if (!$this->createOrUpdatePropertyNames($dto, 'create')) {
            return false;
        }

        $this->logger->info("On create property unit, properties with code '$dto->code' created");
        return true;
    }

    public function update(MessageDTOInterface $dto): bool
    {
        /** @var PropertyUnitMessageDTO $dto */
        if (!$this->createOrUpdatePropertyNames($dto, 'update')) {
            return false;
        }

        $this->logger->info("On update property unit, properties with code '$dto->code' updated");
        return true;
    }

    public function delete(MessageDTOInterface $dto): bool
    {
        /** @var PropertyUnitMessageDTO $dto */
        $properties = $this->getProperties($dto, 'delete');
        if (is_null($properties)) {
            return false;
        }

        foreach ($properties as $property) {
            $property->removeUnit($dto->code);
        }

        $this->logger->info("On delete property unit, properties with code '$dto->code' deleted");
        return true;
    }

    private function createOrUpdatePropertyNames(PropertyUnitMessageDTO $dto, string $fromAction): bool
    {
        $properties = $this->getProperties($dto, $fromAction);
        if (is_null($properties)) {
            return false;
        }

        $names = $this->getPropertyNamesCollectionByDTO($dto->names);

        foreach ($properties as $property) {
            $property->addOrUpdateUnit($dto->code, $names);
        }

        return true;
    }

    private function getProperties(PropertyUnitMessageDTO $dto, string $fromAction): ?array
    {
        /** @var Property[] $properties */
        $properties = $this->propertyRepository->findBy([
            'units.code' => $dto->code
        ]);

        if (empty($properties)) {
            $this->logger->error(
                "On $fromAction property unit, properties with code '$dto->code' not found," .
                ' message: ' . json_encode($dto),
            );
            return null;
        }

        return $properties;
    }

    /**
     * @param PropertyNameMessageDTO[] $dtoNames
     * @return Collection<int, PropertyName>
     */
    private function getPropertyNamesCollectionByDTO(array $dtoNames): Collection
    {
        $propertyNames = new ArrayCollection();

        foreach ($dtoNames as $dtoName) {
            $propertyNames->add(
                (new PropertyName())
                    ->setName($dtoName->name)
                    ->setLocale($dtoName->locale)
            );
        }

        return $propertyNames;
    }
}