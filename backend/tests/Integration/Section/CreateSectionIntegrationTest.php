<?php

namespace App\Tests\Integration\Section;

use App\Tests\Helper\Integration\SectionHelper;
use App\Tests\Integration\AbstractIntegrationTester;

class CreateSectionIntegrationTest extends AbstractIntegrationTester
{
    public function testSuccessCreateWithoutParentId(): void
    {
        $DTO = SectionHelper::createSectionMessageDTO();

        $this->tester->assertNull($this->sectionService->create($DTO));
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
    }

    public function testFailureCreateWithParentId(): void
    {
        $DTO = SectionHelper::createSectionMessageDTO(
            parentId: SectionHelper::PARENT_EXTERNAL_ID
        );

        $this->tester->assertNotNull($this->sectionService->create($DTO));
    }

    public function testFailureCreateWithAlreadyExistingSection(): void
    {
        $this->createSection();

        $DTO = SectionHelper::createSectionMessageDTO();

        $this->tester->assertNotNull($this->sectionService->create($DTO));
    }
}
