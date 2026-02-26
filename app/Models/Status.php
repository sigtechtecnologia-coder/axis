<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $fillable = [
        'context',
        'name',
        'sort_order',
        'color',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public const CONTEXT_QUOTE = 'quote';
    public const CONTEXT_CASE = 'case';

    public static function contexts(): array
    {
        return [
            self::CONTEXT_QUOTE,
            self::CONTEXT_CASE,
        ];
    }

    public function contextLabel(): string
    {
        return match ($this->context) {
            self::CONTEXT_QUOTE => 'Orçamento',
            self::CONTEXT_CASE => 'Esteira',
            default => $this->context,
        };
    }
}