<?php

namespace App\Rules;

use App\Support\BrDocument;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CnpjRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $cnpj = BrDocument::onlyDigits(is_string($value) ? $value : null);

        if ($cnpj === null || !BrDocument::isValidCNPJ($cnpj)) {
            $fail('CNPJ inválido.');
        }
    }
}