<?php

namespace App\Features\Properties\Property\Service;

use Symfony\Component\Validator\Context\ExecutionContextInterface;

final readonly class PropertyValidatorService
{
    public static function validateFilters(?string $filters, ExecutionContextInterface $context): void
    {
        if (empty($filters)) {
            return;
        }

        try {
            $filters = json_decode($filters, true, flags: JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            $context->addViolation('Неверный формат фильтра');
            return;
        }

        if (!is_array($filters)) {
            $context->addViolation('Некорректный фильтр');
            return;
        }

        $keys = array_keys($filters);
        foreach ($keys as $key) {
            if (!is_string($key)) {
                $context->addViolation('Код параметра фильтрации должен быть строкой');
                return;
            }
            if (!is_array($filters[$key])) {
                $context->addViolation('Значения параметра фильтрации должны быть массивом');
                return;
            }
            foreach ($filters[$key] as $value) {
                if (!is_string($value)) {
                    $context->addViolation('Значение параметра фильтрации должно быть строкой');
                    return;
                }
            }
        }
    }

}