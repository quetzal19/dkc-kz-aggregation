<?php

namespace App\Tests\Unit\DTO\Message\ProductFeature;

use App\Features\ProductFeature\DTO\Message\ProductFeaturePrimaryKeyDTO;
use App\Tests\Helper\Unit\BaseUnitTest;
use App\Tests\Helper\Unit\DTO\Message\ProductFeatureHelper;
use Symfony\Component\Validator\Constraints as Assert;

class ProductFeaturePrimaryKeyDTOTest extends BaseUnitTest
{

    public function testNotBlank(): void
    {
        $DTO = new ProductFeaturePrimaryKeyDTO(
            null, null
        );

        $count =  $this->countConstraintsFields(
            $DTO, Assert\NotBlank::class
        );

        $this->assertEquals(count(ProductFeatureHelper::PRIMARY_FIELDS), $count);
    }

    public function testType(): void
    {
        $DTO = new ProductFeaturePrimaryKeyDTO(
            1, 1
        );

        $count =  $this->countConstraintsFields(
            $DTO, Assert\Type::class
        );

        $this->assertEquals(count(ProductFeatureHelper::PRIMARY_FIELDS), $count);
    }

    public function testLength(): void
    {
        $DTO = new ProductFeaturePrimaryKeyDTO(
            '', ''
        );

        $count =  $this->countConstraintsFields(
            $DTO, Assert\Length::class
        );

        $this->assertEquals(count(ProductFeatureHelper::PRIMARY_FIELDS), $count);
    }
}