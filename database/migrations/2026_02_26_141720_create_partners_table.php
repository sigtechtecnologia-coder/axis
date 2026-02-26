<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('partners', function (Blueprint $table) {
            $table->id();

            // Pode ser PF ou PJ
            $table->string('name', 180); // Nome ou Razão Social
            $table->string('type', 2); // PF|PJ

            $table->string('cpf', 11)->nullable();  // somente números
            $table->string('cnpj', 14)->nullable(); // somente números

            $table->string('whatsapp', 20)->nullable();
            $table->string('email', 180)->nullable();

            $table->text('notes')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->unique('cpf');
            $table->unique('cnpj');

            $table->index(['type', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partners');
    }
};