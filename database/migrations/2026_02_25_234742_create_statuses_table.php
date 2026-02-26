<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('statuses', function (Blueprint $table) {
            $table->id();

            // quote = orçamento, case = esteira/atendimento
            $table->string('context', 20);

            $table->string('name', 120);
            $table->unsignedInteger('sort_order')->default(0);

            // Cor para badge (hex tipo #16a34a)
            $table->string('color', 20)->nullable();

            // Em vez de apagar, inativamos
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['context', 'is_active']);
            $table->index(['context', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('statuses');
    }
};