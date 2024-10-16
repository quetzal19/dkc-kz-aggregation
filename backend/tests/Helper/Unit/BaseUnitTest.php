<?php

namespace App\Tests\Helper\Unit;

use App\Helper\Interface\Message\MessageDTOInterface;
use App\Tests\Unit\DTO\Message\EntityHelper;
use Codeception\Test\Unit;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BaseUnitTest extends Unit
{
    protected ValidatorInterface $validator;

    protected function _before(): void
    {
        $this->validator = $this->getModule('Symfony')->_getContainer()->get('validator');
    }

    protected function countConstraintsFields(
        MessageDTOInterface $DTO,
        string $constraintClass,
        array $groups = EntityHelper::GROUP_VALIDATION
    ): int {
        $errors = $this->validator->validate($DTO, groups: $groups);

        $countConstraints = 0;
        foreach ($errors as $error) {
            if ($error->getConstraint() && $error->getConstraint()::class == $constraintClass) {
                $countConstraints++;
            }
        }

        return $countConstraints;
    }
}