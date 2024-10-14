<?php

namespace App\Tests\Integration;

use App\Features\Product\Service\ProductActionService;
use App\Features\Properties\PropertyValue\Service\PropertyValueActionService;
use App\Features\Section\Service\SectionActionService;
use App\Helper\Interface\ActionInterface;
use App\Tests\Helper\Integration\ProductHelper;
use App\Tests\Helper\Integration\Properties\PropertyNameHelper;
use App\Tests\Helper\Integration\Properties\PropertyValueHelper;
use App\Tests\Helper\Integration\SectionHelper;
use App\Tests\Support\IntegrationTester;
use Codeception\Test\Unit;
use Doctrine\ODM\MongoDB\DocumentManager;

class AbstractIntegrationTester extends Unit
{
    protected IntegrationTester $tester;
    protected DocumentManager $documentManager;

    protected ActionInterface $productService;
    protected ActionInterface $sectionService;
    protected ActionInterface $propertyValueService;

    protected function _before(): void
    {
        $this->documentManager = $this->tester->grabService(DocumentManager::class);

        $this->productService = $this->tester->grabService(ProductActionService::class);
        $this->sectionService = $this->tester->grabService(SectionActionService::class);
        $this->propertyValueService = $this->tester->grabService(PropertyValueActionService::class);
    }

    public function createPropertyValue(): void
    {
        $propertyNames = PropertyNameHelper::initPropertyNameByLocales();

        $DTO = PropertyValueHelper::createPropertyValueMessageDTO($propertyNames);

        $this->tester->assertNull($this->propertyValueService->create($DTO));
        $this->documentManager->flush();
    }

    public function createProduct(): void
    {
        $this->createSection();

        $DTO = ProductHelper::createProductMessageDTO(SectionHelper::EXTERNAL_ID);

        $this->tester->assertNull($this->productService->create($DTO));
        $this->documentManager->flush();
    }

    protected function createSection(
        string $name = SectionHelper::NAME,
        string $code = SectionHelper::CODE,
        string $externalId = SectionHelper::EXTERNAL_ID,
        ?string $parentId = null,
    ): void {
        $DTO = SectionHelper::createSectionMessageDTO(
            $name,
            $code,
            $externalId,
            $parentId
        );

        $this->tester->assertNull($this->sectionService->create($DTO));
        $this->documentManager->flush();
    }
}