<?php

namespace App\Helper\Validator;

use App\Helper\{Common\IntegerHelper, Enum\LocaleType, Exception\ApiException};
use Symfony\Component\Validator\{Constraints\NotNull,
    ConstraintViolation,
    ConstraintViolationListInterface,
    Context\ExecutionContextInterface,
    Validator\ValidatorInterface
};
use Symfony\Component\HttpFoundation\Response;

final readonly class ValidatorService
{
    public function __construct(
        private ValidatorInterface $validator,
    ) {
    }

    public function validate($body = [], $groupsBody = []): void
    {
        $groupsBody += ['pagination', 'Default'];
        $bodyError = $this->validator->validate($body, groups: $groupsBody);
        self::checkValidations($bodyError);
    }

    public static function checkValidations(ConstraintViolationListInterface $validations, $payload = null): void
    {
        $validationError = [];
        $invalid_field = [];
        /** @var ConstraintViolation $error */
        foreach ($validations as $error) {
            if ((self::checkInitializationField($payload, $error->getPropertyPath()) ||
                    !($error->getConstraint() instanceof NotNull)) and
                !empty($error->getPropertyPath())
            ) {
                $invalid_field[] = [
                    'name' => $error->getPropertyPath(),
                    'message' => $error->getMessage()
                ];
            }
        }
        $validationError['body'] = $invalid_field;

        if (count($invalid_field) > 0) {
            throw new ApiException(
                message: 'Ошибки при выполнении запроса',
                validationError: $validationError,
                status: Response::HTTP_BAD_REQUEST
            );
        }
    }

    public static function validatePage($object, ExecutionContextInterface $context): void
    {
        if (is_numeric($object)) {
            if ($object <= 0) {
                $context->addViolation("Значение $object должно быть от 1");
            }
        } else {
            $context->addViolation("Некорректное значение, должно быть числом");
        }
    }

    public static function validateLimit($object, ExecutionContextInterface $context): void
    {
        if (is_numeric($object)) {
            if ($object <= 0 || $object > 100) {
                $context->addViolation("Значение $object должно быть от 1 до 100");
            }
        } else {
            $context->addViolation("Некорректное значение, должно быть числом");
        }
    }

    public static function validateInteger($object, ExecutionContextInterface $context): void
    {
        if (is_numeric($object)) {
            if ($object >= IntegerHelper::MAX_SIZE_INTEGER) {
                $context->buildViolation(
                    "Значение $object больше максимально допустимого значения integer."
                )->addViolation();
            }
            if ($object <= IntegerHelper::MIN_SIZE_INTEGER) {
                $context->buildViolation(
                    "Значение $object меньше минимально допустимого значения integer."
                )->addViolation();
            }
        }
    }


    public function validateLocale(?string $locale): void
    {
        if (!in_array($locale, LocaleType::getNamesLocale())) {
            throw new ApiException(
                'Неверно указана локаль, поддерживаемые локали: ' . implode(', ', LocaleType::getNamesLocale()),
            );
        }
    }

    private static function checkInitializationField($payload, $patch): bool
    {
        $payloadField = $payload ? get_object_vars($payload) : [];

        $items = preg_split('/\.|\[|\]/', $patch, -1, PREG_SPLIT_NO_EMPTY);
        $t = $payloadField;
        foreach ($items as $item) {
            if (is_object($t)) {
                $t = get_object_vars($t);
            }
            if (array_key_exists($item, $t)) {
                $t = $t[$item];
            } else {
                return false;
            }
        }
        return true;
    }
}