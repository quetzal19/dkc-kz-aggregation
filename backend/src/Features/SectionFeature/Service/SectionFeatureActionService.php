<?php

namespace App\Features\SectionFeature\Service;

use App\Document\Storage\Temp\Error\ErrorMessage;
use App\Features\TempStorage\Error\Type\ErrorType;
use App\Document\Properties\{Property, SectionCode\SectionCode};
use App\Features\Properties\Property\Repository\PropertyRepository;
use App\Features\Section\Repository\SectionRepository;
use App\Features\SectionFeature\DTO\Message\SectionFeatureMessageDTO;
use App\Helper\Interface\{ActionInterface, Message\MessageDTOInterface};
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use App\Helper\Abstract\Error\AbstractErrorMessage;

final readonly class SectionFeatureActionService implements ActionInterface
{
    public function __construct(
        private PropertyRepository $propertyRepository,
        private SectionRepository $sectionRepository,
        #[Autowire(service: 'monolog.logger.feature_section')]
        private LoggerInterface $logger,
    ) {
    }

    public function create(MessageDTOInterface $dto): ?AbstractErrorMessage
    {
        /** @var SectionFeatureMessageDTO $dto */
        $msg = $this->createOrUpdateSectionFeature($dto, 'create');
        if (!is_null($msg)) {
            return $msg;
        }

        $this->logger->info(
            "facet_feature_section with code '{$dto->primaryKeys->featureCode}' and section '{$dto->primaryKeys->sectionCode}' created"
        );

        return null;
    }

    public function update(MessageDTOInterface $dto): ?AbstractErrorMessage
    {
        /** @var SectionFeatureMessageDTO $dto */
        $msg = $this->createOrUpdateSectionFeature($dto, 'update');
        if (!is_null($msg)) {
            return $msg;
        }

        $this->logger->info(
            "facet_feature_section with code '{$dto->primaryKeys->featureCode}' and section '{$dto->primaryKeys->sectionCode}' updated"
        );

        return null;
    }

    public function delete(MessageDTOInterface $dto): ?AbstractErrorMessage
    {
        /** @var SectionFeatureMessageDTO $dto */
        $property = $this->propertyRepository->findOneBy([
            'code' => $dto->primaryKeys->featureCode,
        ]);

        if (!$property) {
            return new ErrorMessage(
                ErrorType::DATA_NOT_READY,
                "On delete facet_feature_section, feature with code '{$dto->primaryKeys->featureCode}' not found,"
            );
        }

        $section = $this->sectionRepository->findOneBy([
            'code' => $dto->primaryKeys->sectionCode,
        ]);

        if (!$section) {
            return new ErrorMessage(
                ErrorType::DATA_NOT_READY,
                "On delete facet_feature_section, section with code '{$dto->primaryKeys->sectionCode}' not found,"
            );
        }

        $property->removeSectionCodeByCode($section->getCode());

        return null;
    }

    private function createOrUpdateSectionFeature(
        SectionFeatureMessageDTO $dto,
        string $fromAction
    ): ?AbstractErrorMessage {
        $property = $this->propertyRepository->findOneBy([
            'code' => $dto->primaryKeys->featureCode,
        ]);

        if (!$property) {
            return new ErrorMessage(
                ErrorType::DATA_NOT_READY,
                "On $fromAction facet_feature_section, feature with code '{$dto->primaryKeys->featureCode}' not found,"
            );
        }

        $section = $this->sectionRepository->findOneBy([
            'code' => $dto->primaryKeys->sectionCode,
        ]);

        if (!$section) {
            return new ErrorMessage(
                ErrorType::DATA_NOT_READY,
                "On $fromAction facet_feature_section, section with code '{$dto->primaryKeys->sectionCode}' not found,"
            );
        }

        $property->addOrUpdateSectionCode(
            new SectionCode(
                sectionCode: $dto->primaryKeys->sectionCode,
                sort: $dto->sort
            )
        );

        return null;
    }
}