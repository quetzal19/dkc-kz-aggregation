<?php

namespace App\Dto\Api;

use OpenApi\Attributes as OA;

readonly class TestResponse implements ArrayConvertibleDtoInterface
{
    #[OA\Property(example: 'bar')]
    public string $foo;

    public function __construct(string $foo)
    {
        $this->foo = $foo;
    }

    public function toArray(): array
    {
        return [
            'foo' => $this->foo,
        ];
    }
}
