<?php

namespace App\Features\Properties\PropertyUnit\Service;

use App\Helper\Interface\{ActionInterface, Message\MessageDTOInterface};
use App\Document\Storage\Temp\Error\ErrorMessage;
use App\Features\TempStorage\Error\Type\ErrorType;
use App\Document\Properties\{Name\PropertyName, Property};
use App\Features\Properties\Property\Repository\PropertyRepository;
use App\Features\Properties\PropertyName\DTO\Message\PropertyNameMessageDTO;
use App\Features\Properties\PropertyUnit\DTO\Message\PropertyUnitMessageDTO;
use Doctrine\Common\Collections\{ArrayCollection, Collection};
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use App\Helper\Abstract\Error\AbstractErrorMessage;

final readonly class PropertyUnitActionService implements ActionInterface
{
    public function __construct(
        private PropertyRepository $propertyRepository,
        #[Autowire(service: 'monolog.logger.property_unit')]
        private LoggerInterface $logger,
    ) {
    }

    public function create(MessageDTOInterface $dto): ?AbstractErrorMessage
    {
        /** @var PropertyUnitMessageDTO $dto */
        $message = $this->createOrUpdatePropertyNames($dto, 'create');
        if (!is_null($message)) {
            return $message;
        }

        $this->logger->info("On create property unit, properties with code '$dto->code' created");
        return null;
    }

    public function update(MessageDTOInterface $dto): ?AbstractErrorMessage
    {
        /** @var PropertyUnitMessageDTO $dto */
        $message = $this->createOrUpdatePropertyNames($dto, 'update');
        if (!is_null($message)) {
            return $message;
        }

        $this->logger->info("On update property unit, properties with code '$dto->code' updated");
        return null;
    }

    public function delete(MessageDTOInterface $dto): ?AbstractErrorMessage
    {
        /**
         * @var PropertyUnitMessageDTO $dto
         * @var Property[] $properties
         */
        $properties = $this->propertyRepository->findBy([
            'units.code' => $dto->code
        ]);

        if (empty($properties)) {
            return new ErrorMessage(
                ErrorType::DATA_NOT_READY,
                "On delete property unit, properties with code '$dto->code' not found",
            );
        }

        foreach ($properties as $property) {
            $property->removeUnit($dto->code);
        }

        $this->logger->info("On delete property unit, properties with code '$dto->code' deleted");
        return null;
    }

    private function createOrUpdatePropertyNames(PropertyUnitMessageDTO $dto, string $fromAction): ?AbstractErrorMessage
    {
        /** @var Property[] $properties */
        $properties = $this->propertyRepository->findBy([
            'units.code' => $dto->code
        ]);

        if (empty($properties)) {
            return new ErrorMessage(
                ErrorType::DATA_NOT_READY,
                "On $fromAction property unit, properties with code '$dto->code' not found"
            );
        }

        $names = $this->getPropertyNamesCollectionByDTO($dto->names);

        foreach ($properties as $property) {
            $property->addOrUpdateUnit($dto->code, $names);
        }

        return null;
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