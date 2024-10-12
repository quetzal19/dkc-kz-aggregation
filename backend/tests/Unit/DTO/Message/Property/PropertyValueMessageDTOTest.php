<?php

namespace App\Tests\Unit\DTO\Message\Property;

use App\Features\Properties\PropertyValue\DTO\Message\PropertyValueMessageDTO;
use App\Tests\Helper\Unit\BaseUnitTest;
use App\Tests\Helper\Unit\DTO\Message\PropertyValueHelper;
use Symfony\Component\Validator\Constraints as Assert;

class PropertyValueMessageDTOTest extends BaseUnitTest
{
    public function testType(): void
    {
        $DTO = new PropertyValueMessageDTO(code: 1, names: []);

        $count = $this->countConstraintsFields(
            $DTO, Assert\Type::class
        );

        $this->assertEquals(count(PropertyValueHelper::LENGTH_TYPE_FIELDS), $count);
    }

    public function testLength(): void
    {
        $DTO = new PropertyValueMessageDTO(code: "", names: []);

        $count = $this->countConstraintsFields(
            $DTO, Assert\Length::class
        );

        $this->assertEquals(count(PropertyValueHelper::LENGTH_TYPE_FIELDS), $count);
    }

    public function testCount(): void
    {
        $DTO = new PropertyValueMessageDTO(code: "", names: []);

        $count = $this->countConstraintsFields(
            $DTO, Assert\Count::class
        );

        $this->assertEquals(count(PropertyValueHelper::COUNT_NOT_NULL_FIELDS), $count);
    }

    public function testNotNull(): void
    {
        $DTO = new PropertyValueMessageDTO(code: "", names: null);

        $count = $this->countConstraintsFields(
            $DTO, Assert\NotNull::class
        );

        $this->assertEquals(count(PropertyValueHelper::COUNT_NOT_NULL_FIELDS), $count);
    }
}