<?php

namespace App\Http\Requests;

use App\Models\Partner;
use App\Support\BrDocument;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StorePartnerRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'cpf' => BrDocument::onlyDigits($this->input('cpf')),
            'cnpj' => BrDocument::onlyDigits($this->input('cnpj')),
        ]);
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'in:' . implode(',', Partner::types())],
            'name' => ['required', 'string', 'max:180'],

            'cpf' => ['nullable', 'string', 'size:11', 'unique:partners,cpf'],
            'cnpj' => ['nullable', 'string', 'size:14', 'unique:partners,cnpj'],

            'whatsapp' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:180'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            $type = $this->input('type');

            if ($type === Partner::TYPE_PF) {
                $cpf = $this->input('cpf');
                if (!$cpf) {
                    $v->errors()->add('cpf', 'CPF é obrigatório para parceiro PF.');
                } elseif (!BrDocument::isValidCPF($cpf)) {
                    $v->errors()->add('cpf', 'CPF inválido.');
                }
            }

            if ($type === Partner::TYPE_PJ) {
                $cnpj = $this->input('cnpj');
                if (!$cnpj) {
                    $v->errors()->add('cnpj', 'CNPJ é obrigatório para parceiro PJ.');
                } elseif (!BrDocument::isValidCNPJ($cnpj)) {
                    $v->errors()->add('cnpj', 'CNPJ inválido.');
                }
            }
        });
    }
}