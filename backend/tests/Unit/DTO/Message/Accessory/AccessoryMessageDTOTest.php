<?php

namespace App\Tests\Unit\DTO\Message\Accessory;

use App\Features\Accessory\DTO\Message\AccessoryMessageDTO;
use App\Tests\Helper\Unit\BaseUnitTest;
use App\Tests\Helper\Unit\DTO\Message\AccessoryHelper;
use Symfony\Component\Validator\Constraints as Assert;

class AccessoryMessageDTOTest extends BaseUnitTest
{
    public function testNotBlank(): void
    {
        $DTO = new AccessoryMessageDTO(null, null, null, null, null);

        $count = $this->countConstraintsFields(
            $DTO,
            Assert\NotBlank::class
        );

        $this->assertEquals(count(AccessoryHelper::NOT_BLANK_FIELDS), $count);
    }

    public function testType(): void
    {
        $DTO = new AccessoryMessageDTO(1, 1, 1, 1, 1);

        $count = $this->countConstraintsFields(
            $DTO,
            Assert\Type::class
        );

        $this->assertEquals(count(AccessoryHelper::TYPE_FIELDS), $count);
    }
}