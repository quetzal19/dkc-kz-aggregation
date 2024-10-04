<?php

namespace App\Helper\Abstract\Error;

use App\Features\TempStorage\Error\Type\ErrorType;

readonly class AbstractErrorMessage
{
    public ErrorType $errorType;
    public ?string $message;
}