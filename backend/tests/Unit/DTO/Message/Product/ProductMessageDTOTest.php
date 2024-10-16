<?php

namespace App\Tests\Unit\DTO\Message\Product;

use App\Features\Product\DTO\Message\ProductMessageDTO;
use App\Tests\Helper\Unit\BaseUnitTest;
use App\Tests\Helper\Unit\DTO\Message\ProductHelper;
use Symfony\Component\Validator\Constraints as Assert;

class ProductMessageDTOTest extends BaseUnitTest
{

    public function testNotBlank(): void
    {
        $DTO = new ProductMessageDTO(
            null, null, null, null, null, null, null, null, null,
        );

        $count = $this->countConstraintsFields(
            $DTO, Assert\NotBlank::class
        );

        $this->assertEquals(count(ProductHelper::NOT_BLANK_FIELDS), $count);
    }

    public function testType(): void
    {
        $DTO = new ProductMessageDTO(
            1, 1, 1, 1, 1, "", 1, 1, 1,
        );

        $count = $this->countConstraintsFields(
            $DTO, Assert\Type::class
        );

        $this->assertEquals(count(ProductHelper::TYPE_FIELDS), $count);
    }

    public function testNotNull(): void
    {
        $DTO = new ProductMessageDTO(
            null, null, null, null, null, null, null, null, null,
        );

        $count = $this->countConstraintsFields(
            $DTO, Assert\NotNull::class
        );

        $this->assertEquals(count(ProductHelper::NOT_NULL_FIELDS), $count);
    }

}