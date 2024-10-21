<?php

namespace App\Tests\Unit\DTO\Message\CategoryName;

use App\Features\Category\DTO\Message\CategoryNameMessageDTO;
use App\Tests\Helper\Unit\BaseUnitTest;
use App\Tests\Helper\Unit\DTO\Message\CategoryNameHelper;
use Symfony\Component\Validator\Constraints as Assert;

class CategoryNameMessageDTOTest extends BaseUnitTest
{

    public function testNotBlank(): void
    {
        $DTO = new CategoryNameMessageDTO(
            null, null
        );

        $count = $this->countConstraintsFields(
            $DTO,
            Assert\NotBlank::class
        );

        $this->assertEquals(count(CategoryNameHelper::NOT_BLANK_FIELDS), $count);
    }

    public function testType(): void
    {
        $DTO = new CategoryNameMessageDTO(
            1, 1
        );

        $count = $this->countConstraintsFields(
            $DTO,
            Assert\Type::class
        );

        $this->assertEquals(count(CategoryNameHelper::TYPE_FIELDS), $count);
    }

}