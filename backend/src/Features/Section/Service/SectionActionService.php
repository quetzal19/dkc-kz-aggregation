<?php

namespace App\Features\Section\Service;

use App\Document\Section\Section;
use App\Features\Message\Service\MessageValidatorService;
use App\Features\Section\DTO\Message\SectionMessageDTO;
use App\Features\Section\Mapper\SectionMapper;
use App\Features\Section\Repository\SectionRepository;
use App\Helper\Interface\{ActionInterface, Mapper\MapperMessageInterface, Message\MessageDTOInterface};
use App\Helper\Enum\LocaleType;
use Doctrine\ODM\MongoDB\{DocumentManager, MongoDBException};
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Validator\Exception\ValidationFailedException;

final readonly class SectionActionService implements ActionInterface
{
    /** @param SectionMapper $sectionMapper */
    public function __construct(
        private LoggerInterface $logger,
        private SectionRepository $repository,
        private DocumentManager $documentManager,
        private MessageValidatorService $messageValidatorService,
        #[Autowire(service: 'map.section.mapper')]
        private MapperMessageInterface $sectionMapper,
    ) {
    }

    public function create(MessageDTOInterface $dto): bool
    {
        /** @var SectionMessageDTO $dto */
        $section = $this->repository->findOneBy([
            'code' => $dto->code,
            'locale' => LocaleType::fromString($dto->locale)->value
        ]);

        if ($section) {
            $this->logger->error(
                "On create section with code '$dto->code' and locale '$dto->locale' section already exists," .
                " message: " . json_encode($dto)
            );
            return false;
        }

        $newSection = $this->sectionMapper->mapFromMessageDTO($dto);

        try {
            $this->setParentId($newSection, $dto);
        } catch (Exception) {
            return false;
        }

        $this->documentManager->persist($newSection);
        $this->logger->info("Section with code '$dto->code' and locale '$dto->locale' created");
        return true;
    }

    public function update(MessageDTOInterface $dto): bool
    {
        /** @var SectionMessageDTO $dto */
        $section = $this->repository->findOneBy([
            'code' => $dto->code,
            'locale' => LocaleType::fromString($dto->locale)->value
        ]);

        if (!$section) {
            $this->logger->error(
                "On update section with code '$dto->code' and locale '$dto->locale' section not found," .
                " message: " . json_encode($dto)
            );

            try {
                $this->messageValidatorService->validateMessageDTO($dto, ['create']);
            } catch (ValidationFailedException $ex) {
                $this->logger->error(
                    'Post update section, validation for group create failed: ' . $ex->getMessage(
                    ) . ", message: " . json_encode($dto)
                );
                return false;
            }

            return $this->create($dto);
        }

        $this->sectionMapper->mapFromMessageDTO($dto, $section);

        try {
            $this->setParentId($section, $dto);
        } catch (Exception) {
            return false;
        }
        $this->logger->info("Section with code '$dto->code' and locale '$dto->locale' updated");
        return true;
    }

    public function delete(MessageDTOInterface $dto): bool
    {
        /** @var SectionMessageDTO $dto */
        $section = $this->repository->findOneBy([
            'code' => $dto->code,
            'locale' => LocaleType::fromString($dto->locale)->value
        ]);

        if (!$section) {
            $this->logger->error(
                "On delete section with code '$dto->code' and locale '$dto->locale' section not found," .
                " message: " . json_encode($dto)
            );
            return false;
        }

        $this->documentManager->remove($section);
        $this->logger->info("Section with code '$dto->code' and locale '$dto->locale' deleted");
        return true;
    }

    /**
     * @throws Exception
     */
    private function setParentId(Section $section, SectionMessageDTO $dto): void
    {
        if (!empty($dto->parentId)) {
            $parent = $this->repository->findOneBy([
                'externalId' => $dto->parentId
            ]);

            if (!$parent) {
                $this->logger->error(
                    "Parent section with externalId '$dto->parentId' not found," .
                    " message: " . json_encode($dto)
                );
                throw new Exception("Parent section with externalId '$dto->parentId' not found");
            }

            $section->setParent($parent);
        }
    }
}