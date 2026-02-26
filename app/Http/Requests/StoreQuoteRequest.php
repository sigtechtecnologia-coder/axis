<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // Normaliza desconto percentual (trocar vírgula por ponto)
        $dp = $this->input('discount_percent');
        if (is_string($dp)) {
            $dp = str_replace(',', '.', $dp);
        }
        $this->merge(['discount_percent' => $dp]);

        // Normaliza arrays de itens (garante que existam)
        $this->merge([
            'items' => $this->input('items', []),
        ]);
    }

    public function rules(): array
    {
        return [
            'client_id' => ['required', 'exists:clients,id'],
            'status_id' => ['required', 'exists:statuses,id'],
            'notes' => ['nullable', 'string'],

            'discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Adicione pelo menos 1 item.',
            'items.min' => 'Adicione pelo menos 1 item.',
            'items.*.description.required' => 'Descrição do item é obrigatória.',
            'items.*.quantity.required' => 'Quantidade é obrigatória.',
            'items.*.unit_price.required' => 'Valor unitário é obrigatório.',
        ];
    }
}