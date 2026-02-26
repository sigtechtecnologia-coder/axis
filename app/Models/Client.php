<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    public const TYPE_PF = 'PF';
    public const TYPE_PJ = 'PJ';

    protected $fillable = [
        'type',
        'full_name',
        'cpf',
        'company_name',
        'cnpj',
        'responsible_name',
        'responsible_cpf',
        'whatsapp',
        'email',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'display_name',
        'document',
    ];

    /**
     * Retorna SOMENTE os valores válidos (do jeito que o ClientController usa no in_array()).
     */
    public static function types(): array
    {
        return [
            self::TYPE_PF,
            self::TYPE_PJ,
        ];
    }

    /**
     * Nome único para exibição em listas/selects.
     */
    public function getDisplayNameAttribute(): string
    {
        $type = strtoupper((string) $this->type);

        if ($type === self::TYPE_PJ) {
            return (string) ($this->company_name ?: $this->responsible_name ?: ('Cliente #' . $this->id));
        }

        return (string) ($this->full_name ?: $this->responsible_name ?: ('Cliente #' . $this->id));
    }

    /**
     * Documento único (CPF ou CNPJ) para exibição.
     */
    public function getDocumentAttribute(): string
    {
        $type = strtoupper((string) $this->type);

        if ($type === self::TYPE_PJ) {
            return (string) ($this->cnpj ?: '');
        }

        return (string) ($this->cpf ?: '');
    }

    // --- Compatibilidade com views/controllers antigos ---

    public function displayName(): string
    {
        return $this->display_name;
    }

    public function document(): string
    {
        return $this->document;
    }
}