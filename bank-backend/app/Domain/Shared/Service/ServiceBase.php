<?php

declare(strict_types=1);

namespace App\Domain\Shared\Service;

abstract class ServiceBase
{
    private $errors = [];

    public function addError(string $error)
    {
        $this->errors[] = $error;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function setErrors(array $errors): void
    {
        $this->errors = $errors;
    }

    public function hasError(): bool
    {
        return empty($this->errors) === false;
    }

    public function cleanErrors(): void
    {
        $this->errors = [];
    }
}
