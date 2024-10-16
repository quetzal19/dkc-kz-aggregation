<?php

namespace App\Tests\Unit\DTO\Message\Section;

use App\Features\Section\DTO\Message\SectionMessageDTO;
use App\Tests\Helper\Unit\BaseUnitTest;
use App\Tests\Helper\Unit\DTO\Message\SectionHelper;
use Symfony\Component\Validator\Constraints as Assert;

class SectionMessageDTOTest extends BaseUnitTest
{

    public function testNotBlankFields(): void
    {
        $DTO = new SectionMessageDTO();

        $countNotBlank = $this->countConstraintsFields(
            $DTO,Assert\NotBlank::class
        );

        $this->assertEquals(count(SectionHelper::NOT_BLANK_FIELDS), $countNotBlank);
    }

    public function testTypeFields(): void
    {
        $DTO = new SectionMessageDTO( 1,1,1,"1",1,1, 1);

        $countType = $this->countConstraintsFields(
            $DTO,Assert\Type::class
        );

        $this->assertEquals(count(SectionHelper::TYPE_FIELDS), $countType);
    }

    public function testNotNull(): void
    {
        $DTO = new SectionMessageDTO();

        $countType = $this->countConstraintsFields(
            $DTO,Assert\NotNull::class
        );

        $this->assertEquals(count(SectionHelper::NOT_NULL_FIELDS), $countType);
    }

    public function testChoices(): void
    {
        $DTO = new SectionMessageDTO(locale: 'rus');

        $countType = $this->countConstraintsFields(
            $DTO,Assert\Choice::class
        );

        $this->assertEquals(count(SectionHelper::CHOICES_FIELDS), $countType);
    }

}