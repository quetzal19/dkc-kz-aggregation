<?php

namespace App\Tests\Integration\SectionFeature;

use App\Tests\Helper\Integration\{Properties\PropertyHelper, SectionFeatureHelper, SectionHelper};
use App\Tests\Integration\AbstractIntegrationTester;
use MongoDB\Model\{BSONArray, BSONDocument};

class UpdateSectionFeatureIntegrationTest extends AbstractIntegrationTester
{

    public function testSuccessUpdateSortSectionFeature(): void
    {
        $this->createSectionFeature(SectionHelper::PARENT_CODE);

        $DTO = SectionFeatureHelper::createSectionFeatureMessageDTO(
            sectionCode: SectionHelper::PARENT_CODE,
            featureCode: PropertyHelper::CODE,
            sort: SectionFeatureHelper::UPDATED_SORT
        );

        $this->tester->assertNull($this->sectionFeatureService->update($DTO));
        $this->documentManager->flush();

        $property = $this->tester->grabFromCollection('Property', ['code' => PropertyHelper::CODE]);

        $this->tester->assertNotNull($property);
        $this->tester->assertInstanceOf(BSONDocument::class, $property);

        $property = $property->getArrayCopy();

        $this->tester->assertArrayHasKey('sectionCodes', $property);

        $sectionCodes = $property['sectionCodes'];

        $this->tester->assertInstanceOf(BSONArray::class, $sectionCodes);

        $sectionCode = $sectionCodes->getIterator()->current();

        $this->tester->assertInstanceOf(BSONDocument::class, $sectionCode);

        $sectionCode = $sectionCode->getArrayCopy();

        $this->tester->assertArrayHasKey('sectionCode', $sectionCode);
        $this->tester->assertArrayHasKey('sort', $sectionCode);

        $this->tester->assertEquals(SectionHelper::PARENT_CODE, $sectionCode['sectionCode']);
        $this->tester->assertEquals(SectionFeatureHelper::UPDATED_SORT, $sectionCode['sort']);
    }

    public function testFailureUpdateSortSectionFeature(): void
    {
        $DTO = SectionFeatureHelper::createSectionFeatureMessageDTO(
            sectionCode: SectionHelper::CODE,
            featureCode: PropertyHelper::CODE,
            sort: SectionFeatureHelper::UPDATED_SORT
        );

        $this->tester->assertNotNull($this->sectionFeatureService->update($DTO));
        $this->documentManager->flush();
    }
}