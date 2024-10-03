<?php

namespace App\Features\SectionFeature\Service;

use App\Document\Properties\{Property, SectionCode\SectionCode};
use App\Document\Section\Section;
use App\Features\Properties\Property\Repository\PropertyRepository;
use App\Features\Section\Repository\SectionRepository;
use App\Features\SectionFeature\DTO\Message\SectionFeatureMessageDTO;
use App\Helper\Interface\{ActionInterface, Message\MessageDTOInterface};
use Psr\Log\LoggerInterface;

final readonly class SectionFeatureActionService implements ActionInterface
{
    public function __construct(
        private PropertyRepository $propertyRepository,
        private SectionRepository $sectionRepository,
        private LoggerInterface $logger,
    ) {
    }

    public function create(MessageDTOInterface $dto): bool
    {
        /** @var SectionFeatureMessageDTO $dto */
        if (!$this->createOrUpdateSectionFeature($dto, 'create')) {
            return false;
        }

        $this->logger->info(
            "facet_feature_section with code '{$dto->primaryKeys->featureCode}' and section '{$dto->primaryKeys->sectionCode}' created"
        );

        return true;
    }

    public function update(MessageDTOInterface $dto): bool
    {
        /** @var SectionFeatureMessageDTO $dto */
        if (!$this->createOrUpdateSectionFeature($dto, 'update')) {
            return false;
        }

        $this->logger->info(
            "facet_feature_section with code '{$dto->primaryKeys->featureCode}' and section '{$dto->primaryKeys->sectionCode}' updated"
        );

        return true;
    }

    public function delete(MessageDTOInterface $dto): bool
    {
        /** @var SectionFeatureMessageDTO $dto */
        $property = $this->getProperty($dto, 'delete');
        $section = $this->getSection($dto,'delete');
        if (!$property || !$section) {
            return false;
        }

        $property->removeSectionCodeByCode($section->getCode());

        return true;
    }

    private function createOrUpdateSectionFeature(SectionFeatureMessageDTO $dto, string $fromAction): bool
    {
        $property = $this->getProperty($dto, $fromAction);
        $section = $this->getSection($dto, $fromAction);
        if (!$property || !$section) {
            return false;
        }

        $property->addOrUpdateSectionCode(
            new SectionCode(
                sectionCode: $dto->primaryKeys->sectionCode,
                sort: $dto->sort
            )
        );

        return true;
    }

    private function getProperty(SectionFeatureMessageDTO $dto, string $fromAction): ?Property
    {
        $property = $this->propertyRepository->findOneBy([
            'code' => $dto->primaryKeys->featureCode,
        ]);

        if (!$property) {
            $this->logger->error(
                "On $fromAction facet_feature_section, feature with code '{$dto->primaryKeys->featureCode}' not found," .
                " message: " . json_encode($dto)
            );
            return null;
        }

        return $property;
    }

    private function getSection(SectionFeatureMessageDTO $dto, string $fromAction): ?Section
    {
        $section = $this->sectionRepository->findOneBy([
            'code' => $dto->primaryKeys->sectionCode,
        ]);

        if (!$section) {
            $this->logger->error(
                "On $fromAction facet_feature_section, section with code '{$dto->primaryKeys->sectionCode}' not found," .
                " message: " . json_encode($dto)
            );
            return null;
        }

        return $section;
    }
}