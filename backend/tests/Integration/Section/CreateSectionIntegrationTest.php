<?php

namespace App\Tests\Integration\Section;

use App\Tests\Helper\Integration\SectionHelper;
use App\Tests\Integration\AbstractIntegrationTester;
use MongoDB\Model\BSONDocument;

class CreateSectionIntegrationTest extends AbstractIntegrationTester
{
    public function testSuccessCreateWithoutParentId(): void
    {
        $DTO = SectionHelper::createSectionMessageDTO();

        $this->tester->assertNull($this->sectionService->create($DTO));
        $this->documentManager->flush();

        $section = $this->tester->grabFromCollection('Section', ['code' => $DTO->code]);

        $this->tester->assertNotNull($section);
    }

    public function testSuccessCreateWithParentId(): void
    {
        $this->createSection(
            SectionHelper::PARENT_NAME,
            SectionHelper::PARENT_CODE,
            SectionHelper::PARENT_EXTERNAL_ID
        );

        $DTO = SectionHelper::createSectionMessageDTO(
            parentId: SectionHelper::PARENT_EXTERNAL_ID
        );

        $this->tester->assertNull($this->sectionService->create($DTO));
        $this->documentManager->flush();

        $section = $this->tester->grabFromCollection('Section', ['code' => $DTO->code]);

        $this->tester->assertNotNull($section);
        $this->tester->assertInstanceOf(BSONDocument::class, $section);
    }

    public function testFailureCreateWithParentId(): void
    {
        $DTO = SectionHelper::createSectionMessageDTO(
            parentId: SectionHelper::PARENT_EXTERNAL_ID
        );

        $this->tester->assertNotNull($this->sectionService->create($DTO));
        $this->documentManager->flush();

        $section = $this->tester->grabFromCollection('Section', ['code' => $DTO->code]);

        $this->tester->assertNull($section);
    }

    public function testFailureCreateWithAlreadyExistingSection(): void
    {
        $this->createSection();

        $DTO = SectionHelper::createSectionMessageDTO();

        $this->tester->assertNotNull($this->sectionService->create($DTO));
        $this->documentManager->flush();

        $section = $this->tester->grabFromCollection('Section', ['code' => $DTO->code]);

        $this->tester->assertNotNull($section);
    }
}
