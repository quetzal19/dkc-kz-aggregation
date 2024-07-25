<?php

namespace App\Dto\Product;

interface ProductFilterInterface
{
    public function getCode(): string;

    public function getValue(): string;

    public function getUnit(): string;
}
