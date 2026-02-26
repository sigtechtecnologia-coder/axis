<?php

namespace App\Rules;

use App\Support\BrDocument;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CpfRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $cpf = BrDocument::onlyDigits(is_string($value) ? $value : null);

        if ($cpf === null || !BrDocument::isValidCPF($cpf)) {
            $fail('CPF inválido.');
        }
    }
}