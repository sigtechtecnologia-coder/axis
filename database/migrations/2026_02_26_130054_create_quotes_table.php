<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();

            $table->string('number', 30)->unique(); // AX-2026-000001
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();

            // Status de proposta (context=quote)
            $table->foreignId('status_id')->constrained('statuses');

            $table->text('notes')->nullable();

            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount_percent', 5, 2)->nullable(); // 0-100 (opcional)
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);

            $table->timestamps();

            $table->index(['client_id', 'status_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};