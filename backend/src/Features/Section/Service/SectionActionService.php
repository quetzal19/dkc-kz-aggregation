<?php

namespace App\Features\Section\Service;

use App\Document\Section\Section;
use App\Features\Section\DTO\Message\SectionMessageDTO;
use App\Features\Section\Mapper\SectionMapper;
use App\Features\Section\Repository\SectionRepository;
use App\Helper\Interface\{ActionInterface, Mapper\MapperMessageInterface};
use App\Helper\Enum\LocaleType;
use Doctrine\ODM\MongoDB\{DocumentManager, MongoDBException};
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

final readonly class SectionActionService implements ActionInterface
{
    /** @param SectionMapper $sectionMapper */
    public function __construct(
        private LoggerInterface $logger,
        private SerializerInterface $serializer,
        private SectionValidatorService $validatorService,
        private SectionRepository $repository,
        private DocumentManager $documentManager,
        private MapperMessageInterface $sectionMapper,
    ) {
    }

    public function create(array $message): void
    {
        $dto = $this->serializeToDTOAndValidate($message, ['create']);
        if (!$dto) {
            return;
        }

        $section = $this->repository->findOneBy([
            'code' => $dto->code,
            'locale' => LocaleType::fromString($dto->locale)->value
        ]);

        if ($section) {
            $this->logger->error(
                "On create section with code '$dto->code' and locale '$dto->locale' section already exists," .
                " message: " . json_encode($message)
            );
            return;
        }

        $newSection = $this->sectionMapper->mapFromMessageDTO($dto);

        try {
            $this->setParentId($newSection, $dto);
        } catch (Exception) {
            return;
        }

        $this->documentManager->persist($newSection);

        $this->logger->info("Section with code '$dto->code' and locale '$dto->locale' created");
    }

    public function update(array $message): void
    {
        $dto = $this->serializeToDTOAndValidate($message, ['update']);
        if (!$dto) {
            return;
        }

        $section = $this->repository->findOneBy([
            'code' => $dto->code,
            'locale' => LocaleType::fromString($dto->locale)->value
        ]);

        if (!$section) {
            $this->logger->error(
                "On update section with code '$dto->code' and locale '$dto->locale' section not found," .
                " message: " . json_encode($message)
            );
            return;
        }

        $this->sectionMapper->mapFromMessageDTO($dto, $section);

        try {
            $this->setParentId($section, $dto);
        } catch (Exception) {
            return;
        }

        try {
            $this->documentManager->flush();
        } catch (MongoDBException $e) {
            $this->logger->error($e->getMessage());
            return;
        }

        $this->logger->info("Section with code '$dto->code' and locale '$dto->locale' updated");
    }

    public function delete(array $message): void
    {
        $dto = $this->serializeToDTOAndValidate($message, ['delete']);
        if (!$dto) {
            return;
        }

        $section = $this->repository->findOneBy([
            'code' => $dto->code,
            'locale' => LocaleType::fromString($dto->locale)->value
        ]);

        if (!$section) {
            $this->logger->error(
                "On delete section with code '$dto->code' and locale '$dto->locale' section not found," .
                " message: " . json_encode($message)
            );
            return;
        }

        $this->documentManager->remove($section);

        try {
            $this->documentManager->flush();
        } catch (MongoDBException $e) {
            $this->logger->error($e->getMessage());
            return;
        }

        $this->logger->info("Section with code '$dto->code' and locale '$dto->locale' deleted");
    }

    private function serializeToDTOAndValidate(array $message, array $groups): ?SectionMessageDTO
    {
        $dto = $this->serializer->deserialize(
            json_encode($message),
            SectionMessageDTO::class,
            'json'
        );

        try {
            $this->validatorService->validateMessageDTO($dto, $groups);
        } catch (ValidationFailedException $e) {
            $this->logger->error(
                'Validation section failed for group "' . implode(',', $groups) . '": ' .
                 $e->getMessage() . ", message: " .  json_encode($message)
            );
            return null;
        }

        return $dto;
    }

    /**
     * @throws Exception
     */
    private function setParentId(Section $section, SectionMessageDTO $dto): void
    {
        if (!empty($dto->parentId)) {
            $parent = $this->repository->findOneBy([
                'code' => $dto->parentId
            ]);

            if (!$parent) {
                $this->logger->error(
                    "Parent section with code '$dto->parentId' not found," .
                    " message: " . json_encode($dto)
                );
                throw new Exception("Parent section with code '$dto->parentId' not found");
            }

            $section->setParent($parent);
        }
    }
}