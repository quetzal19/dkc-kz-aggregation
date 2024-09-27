<?php

namespace App\Helper\Validator\Attributes;

use App\Helper\Validator\ConstraintValidator\IsValidJsonValidator;
use Symfony\Component\Validator\Constraint;

#[\Attribute]
class IsValidJson extends Constraint
{
    public string $message = 'Невалидный JSON: {{ error }}';

    public function validatedBy(): string
    {
        return IsValidJsonValidator::class;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
