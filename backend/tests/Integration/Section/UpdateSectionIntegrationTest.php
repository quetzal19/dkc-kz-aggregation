<?php

namespace App\Tests\Integration\Section;

use App\Tests\Helper\Integration\SectionHelper;
use App\Tests\Integration\AbstractIntegrationTester;
use App\Tests\Integration\Section\Abstract\AbstractSectionIntegrationTester;

class UpdateSectionIntegrationTest extends AbstractIntegrationTester
{
    public function testSuccessUpdateWithoutParentId(): void
    {
        $this->createSection();

        $DTO = SectionHelper::createSectionMessageDTO(
            active: false,
            sort: SectionHelper::UPDATED_SORT
        );

        $this->tester->assertNull($this->sectionService->update($DTO));
        $this->documentManager->flush();

        $section = $this->tester->grabFromCollection('Section', ['code' => SectionHelper::CODE]);

        $this->tester->assertNotNull($section);

        $this->tester->assertArrayHasKey('sort', $section);
        $this->tester->assertArrayHasKey('active', $section);

        $this->tester->assertEquals(SectionHelper::UPDATED_SORT, $section['sort']);
        $this->tester->assertFalse($section['active']);
    }

    public function testSuccessUpdateWithParentId(): void
    {
        $this->createSection(
            SectionHelper::PARENT_NAME,
            SectionHelper::PARENT_CODE,
            SectionHelper::PARENT_EXTERNAL_ID
        );

        $DTO = SectionHelper::createSectionMessageDTO(
            parentId: SectionHelper::PARENT_EXTERNAL_ID,
            active: false,
            sort: SectionHelper::UPDATED_SORT
        );

        $this->tester->assertNull($this->sectionService->update($DTO));
        $this->documentManager->flush();

        $section = $this->tester->grabFromCollection('Section', ['code' => SectionHelper::CODE]);

        $this->tester->assertNotNull($section);

        $this->tester->assertArrayHasKey('parentCode', $section);
        $this->tester->assertArrayHasKey('sort', $section);
        $this->tester->assertArrayHasKey('active', $section);

        $this->tester->assertEquals(SectionHelper::PARENT_CODE, $section['parentCode']);
        $this->tester->assertEquals($DTO->sort, $section['sort']);
        $this->tester->assertEquals($DTO->active, $section['active']);
    }

    public function testFailureUpdateWithParentId(): void
    {
        $this->createSection();

        $DTO = SectionHelper::createSectionMessageDTO(
            parentId: SectionHelper::PARENT_EXTERNAL_ID
        );

        $this->tester->assertNotNull($this->sectionService->update($DTO));
    }

}