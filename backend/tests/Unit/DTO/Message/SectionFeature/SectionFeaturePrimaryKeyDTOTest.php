<?php

namespace App\Tests\Unit\DTO\Message\SectionFeature;

use App\Features\SectionFeature\DTO\Message\SectionFeaturePrimaryKeyDTO;
use App\Tests\Helper\Unit\BaseUnitTest;
use App\Tests\Helper\Unit\DTO\Message\SectionFeatureHelper;
use Symfony\Component\Validator\Constraints as Assert;

class SectionFeaturePrimaryKeyDTOTest extends BaseUnitTest
{

    public function testNotBlankFields(): void
    {
        $DTO = new SectionFeaturePrimaryKeyDTO(null, null);

        $countNotBlank = $this->countConstraintsFields(
            $DTO, Assert\NotBlank::class
        );

        $this->assertEquals(count(SectionFeatureHelper::PRIMARY_FIELDS), $countNotBlank);
    }

    public function testTypeFields(): void
    {
        $DTO = new SectionFeaturePrimaryKeyDTO(1234, 1234);

        $countType = $this->countConstraintsFields(
            $DTO, Assert\Type::class
        );

        $this->assertEquals(count(SectionFeatureHelper::PRIMARY_FIELDS), $countType);
    }

    public function testLengthFields(): void
    {
        $DTO = new SectionFeaturePrimaryKeyDTO('', '');

        $countLength = $this->countConstraintsFields(
            $DTO, Assert\Length::class
        );

        $this->assertEquals(count(SectionFeatureHelper::PRIMARY_FIELDS), $countLength);
    }

}