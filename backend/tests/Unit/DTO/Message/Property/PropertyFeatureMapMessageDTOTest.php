<?php

namespace App\Tests\Unit\DTO\Message\Property;

use App\Features\Properties\PropertyFeatureMap\DTO\Message\PropertyFeatureMapMessageDTO;
use App\Tests\Helper\Unit\BaseUnitTest;
use App\Tests\Helper\Unit\DTO\Message\PropertyFeatureMapHelper;
use Symfony\Component\Validator\Constraints as Assert;

class PropertyFeatureMapMessageDTOTest extends BaseUnitTest
{
    public function testNotBlank(): void
    {
        $DTO = new PropertyFeatureMapMessageDTO(null, null);

        $count = $this->countConstraintsFields(
            $DTO,
            Assert\NotBlank::class
        );

        $this->assertEquals(count(PropertyFeatureMapHelper::TYPE_NOT_BLANK_FIELDS), $count);
    }

    public function testType(): void
    {
        $DTO = new PropertyFeatureMapMessageDTO(1, []);

        $count = $this->countConstraintsFields(
            $DTO,
            Assert\Type::class
        );

        $this->assertEquals(count(PropertyFeatureMapHelper::TYPE_NOT_BLANK_FIELDS), $count);
    }

    public function testLength(): void
    {
        $DTO = new PropertyFeatureMapMessageDTO("", []);

        $count = $this->countConstraintsFields(
            $DTO,
            Assert\Length::class
        );

        $this->assertEquals(count(PropertyFeatureMapHelper::LENGTH_FIELDS), $count);
    }
}