<?php

namespace App\Http\Requests;

use App\Models\Client;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'cpf' => $this->onlyDigits($this->input('cpf')),
            'cnpj' => $this->onlyDigits($this->input('cnpj')),
            'responsible_cpf' => $this->onlyDigits($this->input('responsible_cpf')),
        ]);
    }

    public function rules(): array
    {
        /** @var \App\Models\Client $client */
        $client = $this->route('client');

        return [
            'type' => ['required', 'in:' . implode(',', Client::types())],

            'full_name' => ['required_if:type,PF', 'nullable', 'string', 'max:150'],
            'cpf' => [
                'required_if:type,PF',
                'nullable',
                'string',
                'size:11',
                Rule::unique('clients', 'cpf')->ignore($client->id),
            ],

            'company_name' => ['required_if:type,PJ', 'nullable', 'string', 'max:180'],
            'cnpj' => [
                'required_if:type,PJ',
                'nullable',
                'string',
                'size:14',
                Rule::unique('clients', 'cnpj')->ignore($client->id),
            ],
            'responsible_name' => ['required_if:type,PJ', 'nullable', 'string', 'max:150'],
            'responsible_cpf' => ['required_if:type,PJ', 'nullable', 'string', 'size:11'],

            'whatsapp' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:180'],

            'is_active' => ['required', 'boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            $type = $this->input('type');

            if ($type === Client::TYPE_PF) {
                $cpf = $this->input('cpf');
                if ($cpf && !$this->isValidCPF($cpf)) {
                    $v->errors()->add('cpf', 'CPF inválido.');
                }
            }

            if ($type === Client::TYPE_PJ) {
                $cnpj = $this->input('cnpj');
                if ($cnpj && !$this->isValidCNPJ($cnpj)) {
                    $v->errors()->add('cnpj', 'CNPJ inválido.');
                }

                $rcpf = $this->input('responsible_cpf');
                if ($rcpf && !$this->isValidCPF($rcpf)) {
                    $v->errors()->add('responsible_cpf', 'CPF do responsável inválido.');
                }
            }
        });
    }

    private function onlyDigits(?string $value): ?string
    {
        if ($value === null) return null;
        $digits = preg_replace('/\D+/', '', $value);
        return $digits === '' ? null : $digits;
    }

    private function isValidCPF(string $cpf): bool
    {
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

    private function isValidCNPJ(string $cnpj): bool
    {
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