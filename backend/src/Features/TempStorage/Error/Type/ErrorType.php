<?php

namespace App\Features\TempStorage\Error\Type;

enum ErrorType: string
{
    case DATA_NOT_READY = 'DATA_NOT_READY';
    case VALIDATION_ERROR = 'VALIDATION_ERROR';
    case ENTITY_ALREADY_EXISTS = 'ENTITY_ALREADY_EXISTS';
    case DUPLICATE = 'DUPLICATE';
    case ENTITY_NOT_FOUND = 'ENTITY_NOT_FOUND';
    case UNKNOWN_ERROR = 'UNKNOWN_ERROR';

    public static function getTypesForRemoveStorage(): array
    {
        return [
            self::VALIDATION_ERROR,
            self::ENTITY_NOT_FOUND,
            self::ENTITY_ALREADY_EXISTS,
            self::UNKNOWN_ERROR,
            self::DUPLICATE,
        ];
    }
}