<?php

namespace App\Helper\Validator\ConstraintValidator;

use App\Helper\Validator\Attributes\IsValidJson;
use JsonException;
use Symfony\Component\Validator\{Constraint, ConstraintValidator};

class IsValidJsonValidator extends ConstraintValidator
{
    /**
     * @param IsValidJson $constraint
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (empty($value)) {
            return;
        }

        if (is_array($value)) {
            $value = json_encode($value);
        }

        if (!is_string($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ error }}', 'неверный тип данных')
                ->addViolation();
        }

        try {
            $decodedJson = json_decode($value, false, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ error }}', $e->getMessage())
                ->addViolation();
            return;
        }

        if (!is_array($decodedJson) && !is_object($decodedJson)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ error }}', 'неверный тип данных, ожидается объект или массив')
                ->addViolation();
        }
    }
}
