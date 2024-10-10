<?php

namespace App\Document\Storage\Temp\Error;

use App\Features\TempStorage\Error\Type\ErrorType;
use App\Helper\Abstract\Error\AbstractErrorMessage;
use Doctrine\ODM\MongoDB\{Mapping\Annotations as MongoDB, Types\Type};

#[MongoDB\EmbeddedDocument]
final readonly class ErrorMessage extends AbstractErrorMessage
{
    public function __construct(
        #[MongoDB\Field(type: 'string', enumType: ErrorType::class)]
        public ErrorType $errorType,

        #[MongoDB\Field(type: Type::STRING, nullable: true)]
        public ?string $message = null,
    ) {
    }
}