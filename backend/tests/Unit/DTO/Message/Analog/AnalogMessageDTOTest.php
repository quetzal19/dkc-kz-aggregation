<?php

namespace App\Tests\Unit\DTO\Message\Analog;

use App\Features\Analog\DTO\Message\AnalogMessageDTO;
use App\Tests\Helper\Unit\BaseUnitTest;
use App\Tests\Helper\Unit\DTO\Message\AnalogHelper;
use Symfony\Component\Validator\Constraints as Assert;

class AnalogMessageDTOTest extends BaseUnitTest
{
    public function testNotBlank(): void
    {
        $DTO = new AnalogMessageDTO(null, null, null, null, null);

        $count = $this->countConstraintsFields(
            $DTO,
            Assert\NotBlank::class
        );

        $this->assertEquals(count(AnalogHelper::NOT_BLANK_FIELDS), $count);
    }

    public function testType(): void
    {
        $DTO = new AnalogMessageDTO(1, 1, 1, 1, null);

        $count = $this->countConstraintsFields(
            $DTO,
            Assert\Type::class
        );

        $this->assertEquals(count(AnalogHelper::TYPE_FIELDS), $count);
    }

    public function testNotNull(): void
    {
        $DTO = new AnalogMessageDTO(null, null, null, null, null);

        $count = $this->countConstraintsFields(
            $DTO,
            Assert\NotNull::class
        );

        $this->assertEquals(count(AnalogHelper::NOT_NULL_FIELDS), $count);
    }
}