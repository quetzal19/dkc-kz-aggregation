<?php

namespace App\Tests\Integration\Section;


use App\Tests\Helper\Integration\SectionHelper;
use App\Tests\Integration\AbstractIntegrationTester;
use App\Tests\Integration\Section\Abstract\AbstractSectionIntegrationTester;

class DeleteSectionIntegrationTest extends AbstractIntegrationTester
{
    public function testSuccessDelete(): void
    {
        $this->createSection();

        $DTO = SectionHelper::createSectionMessageDTO();

        $this->tester->assertNull($this->sectionService->delete($DTO));
    }


    public function testFailureDelete(): void
    {
        $DTO = SectionHelper::createSectionMessageDTO();

        $this->tester->assertNotNull($this->sectionService->delete($DTO));
    }


    public function testSuccessDeleteWithParent(): void
    {
        $this->createSection(
            SectionHelper::PARENT_NAME,
            SectionHelper::PARENT_CODE,
            SectionHelper::PARENT_EXTERNAL_ID
        );

        $this->createSection(parentId: SectionHelper::PARENT_EXTERNAL_ID);

        $DTO = SectionHelper::createSectionMessageDTO(parentId: SectionHelper::PARENT_EXTERNAL_ID);

        $parentDTO = SectionHelper::createSectionMessageDTO(
            SectionHelper::PARENT_NAME,
            SectionHelper::PARENT_CODE,
            SectionHelper::PARENT_EXTERNAL_ID
        );

        $this->tester->assertNull($this->sectionService->delete($DTO));
        $this->tester->assertNull($this->sectionService->delete($parentDTO));
    }

}