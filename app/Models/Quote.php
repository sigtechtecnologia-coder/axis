<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quote extends Model
{
    protected $fillable = [
        'number',
        'client_id',
        'status_id',
        'notes',
        'subtotal',
        'discount_percent',
        'discount_amount',
        'total',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function quoteServices(): HasMany
    {
        return $this->hasMany(QuoteService::class, 'quote_id');
    }

    public function recalcTotals(): void
    {
        $subtotal = $this->quoteServices->sum(fn ($row) => (float) $row->price);

        $discountPercent = $this->discount_percent;
        $discountPercent = $discountPercent === null ? 0.0 : (float) $discountPercent;

        $discountAmount = round($subtotal * ($discountPercent / 100), 2);
        $total = round($subtotal - $discountAmount, 2);

        $this->subtotal = $subtotal;
        $this->discount_amount = $discountAmount;
        $this->total = $total;
    }
}