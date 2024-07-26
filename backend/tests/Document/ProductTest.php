<?php

namespace App\Tests\Document;

use App\Document\Product;
use App\Dto\Product\ProductDto;
use App\Dto\Product\ProductFilterDto;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{

    public function testFromDto(): void
    {
        $productDto = new ProductDto([
            'code' => 'code',
            'sectionCode' => 'sectionCode',
            'name' => 'name',
            'weight' => 'weight',
            'volume' => 'volume',
            'filters' => []
        ]);

        $product = Product::fromDto($productDto);

        $this->assertEquals($productDto->getCode(), $product->getCode());
        $this->assertEquals($productDto->getSectionCode(), $product->getSectionCode());
        $this->assertEquals($productDto->getName(), $product->getName());
        $this->assertEquals($productDto->getWeight(), $product->getWeight());
        $this->assertEquals($productDto->getVolume(), $product->getVolume());
    }

    public function testSetFilters(): void
    {
        $pattern = '%s:%s:%s';
        $firstFilterArray = [
            'code' => 'color',
            'value' => 'blue',
            'unit' => 'string'
        ];
        $secondFilterArray = [
            'code' => 'size',
            'value' => 'large',
            'unit' => 'string'
        ];

        $firstFilterString = sprintf(
            $pattern,
            $firstFilterArray['code'],
            $firstFilterArray['value'],
            $firstFilterArray['unit']
        );
        $secondFilterString = sprintf(
            $pattern,
            $secondFilterArray['code'],
            $secondFilterArray['value'],
            $secondFilterArray['unit']
        );

        $filters = [
            new ProductFilterDto($firstFilterArray),
            new ProductFilterDto($secondFilterArray)
        ];

        $product = new Product();
        $product->setFilters($filters);

        $this->assertCount(2, $product->getFilters());
        $this->assertEquals($firstFilterString, $product->getFilters()[0]);
        $this->assertEquals($secondFilterString, $product->getFilters()[1]);
    }
}
