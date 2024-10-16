<?php

namespace App\Tests\Unit\DTO\Message\Property;

use App\Features\Properties\PropertyName\DTO\Message\PropertyNameMessageDTO;
use App\Tests\Helper\Unit\BaseUnitTest;
use App\Tests\Helper\Unit\DTO\Message\PropertyNameHelper;
use Symfony\Component\Validator\Constraints as Assert;

class PropertyNameMessageDTOTest extends BaseUnitTest
{

    public function testNotBlank(): void
    {
        $DTO = new PropertyNameMessageDTO(null, null);

        $count = $this->countConstraintsFields(
            $DTO, Assert\NotBlank::class
        );

        $this->assertEquals(count(PropertyNameHelper::NOT_BLANK_CHOICES_FIELDS), $count);
    }

    public function testType(): void
    {
        $DTO = new PropertyNameMessageDTO(123, 123);

        $count = $this->countConstraintsFields(
            $DTO, Assert\Type::class
        );

        $this->assertEquals(count(PropertyNameHelper::TYPE_FIELDS), $count);
    }

    public function testChoice(): void
    {
        $DTO = new PropertyNameMessageDTO(null, "rus");

        $count = $this->countConstraintsFields(
            $DTO, Assert\Choice::class
        );

        $this->assertEquals(count(PropertyNameHelper::NOT_BLANK_CHOICES_FIELDS), $count);
    }
}
