<?php

namespace App\Tests\Integration\SectionFeature;

use App\Tests\Helper\Integration\{Properties\PropertyHelper, SectionFeatureHelper, SectionHelper};
use App\Tests\Integration\AbstractIntegrationTester;
use MongoDB\Model\{BSONArray, BSONDocument};

class DeleteSectionFeatureIntegrationTest extends AbstractIntegrationTester
{

    public function testSuccessDeleteSectionFeature(): void
    {
        $this->createSectionFeature();

        $DTO = SectionFeatureHelper::createSectionFeatureMessageDTO(
            sectionCode: SectionHelper::CODE,
            featureCode: PropertyHelper::CODE
        );

        // Test sectionCodes empty in property
        $this->tester->assertNull($this->sectionFeatureService->delete($DTO));
        $this->documentManager->flush();

        $property = $this->tester->grabFromCollection('Property', ['code' => PropertyHelper::CODE]);

        $this->tester->assertInstanceOf(BSONDocument::class, $property);

        $property = $property->getArrayCopy();

        $this->tester->assertArrayHasKey('sectionCodes', $property);

        $sectionCodes = $property['sectionCodes'];

        $this->tester->assertInstanceOf(BSONArray::class, $sectionCodes);

        $sectionCodes = $sectionCodes->getArrayCopy();

        $this->tester->assertEmpty($sectionCodes);
    }

    public function testFailureDeleteSectionFeature(): void
    {
        $DTO = SectionFeatureHelper::createSectionFeatureMessageDTO(
            sectionCode: SectionHelper::CODE,
            featureCode: PropertyHelper::CODE
        );

        $this->tester->assertNotNull($this->sectionFeatureService->delete($DTO));
        $this->documentManager->flush();
    }
}