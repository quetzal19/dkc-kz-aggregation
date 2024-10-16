<?php

namespace App\Tests\Integration\SectionFeature;

use App\Tests\Helper\Integration\{Properties\PropertyHelper, SectionFeatureHelper, SectionHelper};
use App\Tests\Integration\AbstractIntegrationTester;
use MongoDB\Model\{BSONArray, BSONDocument};

class CreateSectionFeatureIntegrationTest extends AbstractIntegrationTester
{
    public function testSuccessCreateSectionFeature(): void
    {
        $this->createSection();
        $this->createProperty();

        $DTO = SectionFeatureHelper::createSectionFeatureMessageDTO(
            sectionCode: SectionHelper::CODE,
            featureCode: PropertyHelper::CODE
        );

        $this->tester->assertNull($this->sectionFeatureService->create($DTO));
        $this->documentManager->flush();

        // Test get from database section feature
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

        $this->tester->assertEquals(SectionHelper::CODE, $sectionCode['sectionCode']);
        $this->tester->assertEquals(SectionFeatureHelper::SORT, $sectionCode['sort']);
    }

    public function testFailureCreateSectionFeature(): void
    {
        $this->createProperty();

        $DTO = SectionFeatureHelper::createSectionFeatureMessageDTO(
            sectionCode: SectionHelper::CODE,
            featureCode: PropertyHelper::CODE
        );

        $this->tester->assertNotNull($this->sectionFeatureService->create($DTO));
        $this->documentManager->flush();

        $property = $this->tester->grabFromCollection('Property', ['code' => PropertyHelper::CODE]);

        $this->tester->assertNotNull($property);
        $this->tester->assertInstanceOf(BSONDocument::class, $property);

        $property = $property->getArrayCopy();

        $this->tester->assertArrayNotHasKey('sectionCodes', $property);
    }
}