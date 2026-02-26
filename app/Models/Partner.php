<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    protected $fillable = [
        'name',
        'type',
        'cpf',
        'cnpj',
        'whatsapp',
        'email',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public const TYPE_PF = 'PF';
    public const TYPE_PJ = 'PJ';

    public static function types(): array
    {
        return [self::TYPE_PF, self::TYPE_PJ];
    }

    public function document(): ?string
    {
        return $this->type === self::TYPE_PJ ? $this->cnpj : $this->cpf;
    }
}