<?php

namespace App\Support;

final class BrDocument
{
    public static function onlyDigits(?string $value): ?string
    {
        if ($value === null) return null;

        $digits = preg_replace('/\D+/', '', $value);
        return ($digits === '' ? null : $digits);
    }

    public static function isValidCPF(?string $cpf): bool
    {
        if ($cpf === null) return false;

        if (!preg_match('/^\d{11}$/', $cpf)) return false;
        if (preg_match('/^(\d)\1{10}$/', $cpf)) return false;

        $sum = 0;
        for ($i = 0; $i < 9; $i++) $sum += (int)$cpf[$i] * (10 - $i);
        $d1 = 11 - ($sum % 11);
        $d1 = $d1 >= 10 ? 0 : $d1;

        $sum = 0;
        for ($i = 0; $i < 10; $i++) $sum += (int)$cpf[$i] * (11 - $i);
        $d2 = 11 - ($sum % 11);
        $d2 = $d2 >= 10 ? 0 : $d2;

        return ((int)$cpf[9] === $d1) && ((int)$cpf[10] === $d2);
    }

    public static function isValidCNPJ(?string $cnpj): bool
    {
        if ($cnpj === null) return false;

        if (!preg_match('/^\d{14}$/', $cnpj)) return false;
        if (preg_match('/^(\d)\1{13}$/', $cnpj)) return false;

        $weights1 = [5,4,3,2,9,8,7,6,5,4,3,2];
        $weights2 = [6,5,4,3,2,9,8,7,6,5,4,3,2];

        $sum = 0;
        for ($i = 0; $i < 12; $i++) $sum += (int)$cnpj[$i] * $weights1[$i];
        $d1 = $sum % 11;
        $d1 = $d1 < 2 ? 0 : 11 - $d1;

        $sum = 0;
        for ($i = 0; $i < 13; $i++) $sum += (int)$cnpj[$i] * $weights2[$i];
        $d2 = $sum % 11;
        $d2 = $d2 < 2 ? 0 : 11 - $d2;

        return ((int)$cnpj[12] === $d1) && ((int)$cnpj[13] === $d2);
    }
}