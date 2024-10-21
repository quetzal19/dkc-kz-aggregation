<?php

namespace App\Tests\Unit\DTO\Message\Property;

use App\Features\Properties\Property\DTO\Message\PropertyMessageDTO;
use App\Tests\Helper\Unit\BaseUnitTest;
use App\Tests\Helper\Unit\DTO\Message\PropertyHelper;
use Symfony\Component\Validator\Constraints as Assert;

class PropertyMessageDTOTest extends BaseUnitTest
{

    public function testNotBlank(): void
    {
        $DTO = new PropertyMessageDTO(null, null);

        $count = $this->countConstraintsFields(
            $DTO,
            Assert\NotBlank::class
        );

        $this->assertEquals(count(PropertyHelper::NOT_BLANK_TYPE), $count);
    }

    public function testType(): void
    {
        $DTO = new PropertyMessageDTO(1, null);

        $count = $this->countConstraintsFields(
            $DTO,
            Assert\Type::class
        );

        $this->assertEquals(count(PropertyHelper::NOT_BLANK_TYPE), $count);
    }

    public function testCountNotNull(): void
    {
        $DTO = new PropertyMessageDTO(null, null);

        $count = $this->countConstraintsFields(
            $DTO,
            Assert\NotNull::class
        );

        $this->assertEquals(count(PropertyHelper::COUNT_NOT_NULL), $count);
    }

    public function testCount(): void
    {
        $DTO = new PropertyMessageDTO(null, []);

        $count = $this->countConstraintsFields($DTO, Assert\Count::class);

        $this->assertEquals(count(PropertyHelper::COUNT_NOT_NULL), $count);
    }

}