<?php

namespace App\Features\Section\Service;

use App\Document\Section\Section;
use App\Document\Storage\Temp\Error\ErrorMessage;
use App\Features\Message\Service\MessageValidatorService;
use App\Features\Section\DTO\Message\SectionMessageDTO;
use App\Features\Section\Mapper\SectionMapper;
use App\Features\Section\Repository\SectionRepository;
use App\Helper\Interface\{ActionInterface, Mapper\MapperMessageInterface, Message\MessageDTOInterface};
use App\Features\TempStorage\Error\Type\ErrorType;
use App\Helper\Abstract\Error\AbstractErrorMessage;
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
        #[Autowire(service: 'monolog.logger.section')]
        private LoggerInterface $logger,
        private SectionRepository $repository,
        private DocumentManager $documentManager,
        private MessageValidatorService $messageValidatorService,
        #[Autowire(service: 'map.section.mapper')]
        private MapperMessageInterface $sectionMapper,
    ) {
    }

    public function create(MessageDTOInterface $dto): ?AbstractErrorMessage
    {
        /** @var SectionMessageDTO $dto */
        $section = $this->repository->findOneBy([
            'code' => $dto->code,
            'locale' => LocaleType::fromString($dto->locale)->value
        ]);

        if ($section) {
            return new ErrorMessage(
                ErrorType::ENTITY_ALREADY_EXISTS,
                "On create section with code '$dto->code' and locale '$dto->locale' section already exists"
            );
        }

        $newSection = $this->sectionMapper->mapFromMessageDTO($dto);

        try {
            $this->setParentId($newSection, $dto);
        } catch (Exception $ex) {
            return new ErrorMessage(
                ErrorType::DATA_NOT_READY,
                $ex->getMessage(),
            );
        }

        $this->documentManager->persist($newSection);
        $this->logger->info("Section with code '$dto->code' and locale '$dto->locale' created");
        return null;
    }

    public function update(MessageDTOInterface $dto): ?AbstractErrorMessage
    {
        /** @var SectionMessageDTO $dto */
        $section = $this->repository->findOneBy([
            'code' => $dto->code,
            'locale' => LocaleType::fromString($dto->locale)->value
        ]);

        if (!$section) {
            $this->logger->warning(
                "On update section with code '$dto->code' and locale '$dto->locale' section not found," .
                " message: " . json_encode($dto)
            );

            try {
                $this->messageValidatorService->validateMessageDTO($dto, ['create']);
            } catch (ValidationFailedException $ex) {
                return new ErrorMessage(
                    ErrorType::VALIDATION_ERROR,
                    'Post update section, validation for group create failed: ' . $ex->getMessage()
                );
            }

            return $this->create($dto);
        }

        $this->sectionMapper->mapFromMessageDTO($dto, $section);

        try {
            $this->setParentId($section, $dto);
        } catch (Exception $ex) {
            return new ErrorMessage(
                ErrorType::DATA_NOT_READY,
                $ex->getMessage(),
            );
        }

        $this->logger->info("Section with code '$dto->code' and locale '$dto->locale' updated");
        return null;
    }

    public function delete(MessageDTOInterface $dto): ?AbstractErrorMessage
    {
        /** @var SectionMessageDTO $dto */
        $section = $this->repository->findOneBy([
            'code' => $dto->code,
            'locale' => LocaleType::fromString($dto->locale)->value
        ]);

        if (!$section) {
            return new ErrorMessage(
                ErrorType::ENTITY_NOT_FOUND,
                "On delete section with code '$dto->code' and locale '$dto->locale' section not found"
            );
        }

        $this->documentManager->remove($section);
        $this->logger->info("Section with code '$dto->code' and locale '$dto->locale' deleted");
        return null;
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
                throw new Exception("Parent section with externalId '$dto->parentId' not found");
            }

            $section->setParent($parent);
        }
    }
}