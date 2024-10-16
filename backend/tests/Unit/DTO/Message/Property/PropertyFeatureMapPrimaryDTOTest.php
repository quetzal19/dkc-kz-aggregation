<?php

namespace App\Tests\Unit\DTO\Message\Property;

use App\Features\Properties\PropertyFeatureMap\DTO\Message\PropertyFeatureMapPrimaryKeyDTO;
use App\Tests\Helper\Unit\BaseUnitTest;
use App\Tests\Helper\Unit\DTO\Message\PropertyFeatureMapHelper;
use Symfony\Component\Validator\Constraints as Assert;

class PropertyFeatureMapPrimaryDTOTest extends BaseUnitTest
{

    public function testNotBlank(): void
    {
        $DTO = new PropertyFeatureMapPrimaryKeyDTO(null, null);

        $count = $this->countConstraintsFields($DTO, Assert\NotBlank::class);

        $this->assertEquals(count(PropertyFeatureMapHelper::PRIMARY_FIELDS), $count);
    }

    public function testType(): void
    {
        $DTO = new PropertyFeatureMapPrimaryKeyDTO(1, 1);

        $count = $this->countConstraintsFields($DTO, Assert\Type::class);

        $this->assertEquals(count(PropertyFeatureMapHelper::PRIMARY_FIELDS), $count);
    }

    public function testLength(): void
    {
        $DTO = new PropertyFeatureMapPrimaryKeyDTO("", "");

        $count = $this->countConstraintsFields($DTO, Assert\Length::class);

        $this->assertEquals(count(PropertyFeatureMapHelper::PRIMARY_FIELDS), $count);
    }
}