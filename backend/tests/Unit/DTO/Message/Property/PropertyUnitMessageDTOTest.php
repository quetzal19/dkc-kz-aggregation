<?php

namespace App\Tests\Unit\DTO\Message\Property;

use App\Features\Properties\PropertyUnit\DTO\Message\PropertyUnitMessageDTO;
use App\Tests\Helper\Unit\BaseUnitTest;
use App\Tests\Helper\Unit\DTO\Message\PropertyUnitHelper;
use Symfony\Component\Validator\Constraints as Assert;

class PropertyUnitMessageDTOTest extends BaseUnitTest
{

    public function testNotBlank(): void
    {
        $DTO = new PropertyUnitMessageDTO(null,null);

        $count = $this->countConstraintsFields(
            $DTO, Assert\NotBlank::class,
        );

        $this->assertEquals(count(PropertyUnitHelper::NOT_BLANK_TYPE_FIELDS), $count);
    }

    public function testNotNull(): void
    {
        $DTO = new PropertyUnitMessageDTO(null, null);

        $count = $this->countConstraintsFields(
            $DTO, Assert\NotNull::class
        );

        $this->assertEquals(count(PropertyUnitHelper::COUNT_NOT_NULL), $count);
    }

    public function testType(): void
    {
        $DTO = new PropertyUnitMessageDTO(1, []);

        $count = $this->countConstraintsFields(
            $DTO, Assert\Type::class
        );

        $this->assertEquals(count(PropertyUnitHelper::NOT_BLANK_TYPE_FIELDS), $count);
    }

    public function testCount(): void
    {
        $DTO = new PropertyUnitMessageDTO(null, []);

        $count = $this->countConstraintsFields(
            $DTO, Assert\Count::class
        );

        $this->assertEquals(count(PropertyUnitHelper::COUNT_NOT_NULL), $count);
    }
}