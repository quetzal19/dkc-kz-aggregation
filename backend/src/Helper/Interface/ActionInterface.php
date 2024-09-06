<?php

namespace App\Helper\Interface;

interface ActionInterface
{
    public function create(array $message): void;
    public function update(array $message): void;
    public function delete(array $message): void;
}