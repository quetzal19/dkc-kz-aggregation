<?php

namespace App\Tests\Unit\DTO\Message\Product;

use App\Features\ProductFeature\DTO\Message\ProductFeatureMessageDTO;
use App\Tests\Helper\Unit\BaseUnitTest;
use App\Tests\Helper\Unit\DTO\Message\ProductFeatureHelper;
use Symfony\Component\Validator\Constraints as Assert;

class ProductFeatureMessageDTOTest extends BaseUnitTest
{

    public function testNotBlank(): void
    {
        $DTO = new ProductFeatureMessageDTO(
          null,null,null,
        );

        $count = $this->countConstraintsFields(
            $DTO, Assert\NotBlank::class
        );

        $this->assertEquals(count(ProductFeatureHelper::NOT_BLANK), $count);
    }

    public function testType(): void
    {
        $DTO = new ProductFeatureMessageDTO(
            [],1,1,
        );

        $count = $this->countConstraintsFields(
            $DTO, Assert\Type::class
        );

        $this->assertEquals(count(ProductFeatureHelper::TYPE_FIELDS), $count);
    }
}