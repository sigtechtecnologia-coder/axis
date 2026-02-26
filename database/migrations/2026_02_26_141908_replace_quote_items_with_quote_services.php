<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Se já existir, remove
        if (Schema::hasTable('quote_items')) {
            Schema::drop('quote_items');
        }

        Schema::create('quote_services', function (Blueprint $table) {
            $table->id();

            $table->foreignId('quote_id')->constrained('quotes')->cascadeOnDelete();

            $table->foreignId('service_id')->constrained('services');
            $table->foreignId('partner_id')->constrained('partners');

            $table->decimal('price', 12, 2)->default(0);

            $table->timestamps();

            $table->index('quote_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quote_services');

        // opcional: recriar quote_items (não vou recriar para não confundir)
        Schema::create('quote_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->constrained('quotes')->cascadeOnDelete();
            $table->string('description', 255);
            $table->decimal('quantity', 12, 2)->default(1);
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->timestamps();
        });
    }
};