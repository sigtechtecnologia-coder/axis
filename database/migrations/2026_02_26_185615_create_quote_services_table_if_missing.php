<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('quote_services')) {
            return;
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
    }
};