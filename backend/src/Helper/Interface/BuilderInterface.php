<?php

namespace App\Helper\Interface;

interface BuilderInterface
{
    public function build(): object;

    public static function create(): BuilderInterface;
}