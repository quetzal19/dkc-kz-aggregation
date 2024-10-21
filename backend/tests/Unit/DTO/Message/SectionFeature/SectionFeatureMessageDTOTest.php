<?php

namespace App\Tests\Unit\DTO\Message\SectionFeature;

use App\Features\SectionFeature\DTO\Message\SectionFeatureMessageDTO;
use App\Helper\Common\IntegerHelper;
use App\Tests\Helper\Unit\BaseUnitTest;
use App\Tests\Helper\Unit\DTO\Message\SectionFeatureHelper;
use Symfony\Component\Validator\Constraints as Assert;

class SectionFeatureMessageDTOTest extends BaseUnitTest
{
    public function testNotBlankFields(): void
    {
        $DTO = new SectionFeatureMessageDTO(null, null);

        $countNotBlank = $this->countConstraintsFields(
            $DTO, Assert\NotBlank::class
        );

        $this->assertEquals(count(SectionFeatureHelper::NOT_BLANK_FIELDS), $countNotBlank);
    }

    public function testTypeFields(): void
    {
        $DTO = new SectionFeatureMessageDTO([], 'test');

        $countType = $this->countConstraintsFields(
            $DTO, Assert\Type::class
        );

        $this->assertEquals(count(SectionFeatureHelper::TYPE_FIELDS), $countType);
    }

    public function testPositiveOrZero(): void
    {
        $DTO = new SectionFeatureMessageDTO([], -1);

        $countPositiveOrZero = $this->countConstraintsFields(
            $DTO, Assert\PositiveOrZero::class
        );

        $this->assertEquals(count(SectionFeatureHelper::INTEGER_VALID_FIELDS), $countPositiveOrZero);
    }

    public function testLessThan(): void
    {
        $DTO = new SectionFeatureMessageDTO([], IntegerHelper::MAX_SIZE_INTEGER + 1);

        $countLessThan = $this->countConstraintsFields(
            $DTO, Assert\LessThan::class
        );

        $this->assertEquals(count(SectionFeatureHelper::INTEGER_VALID_FIELDS), $countLessThan);
    }
}