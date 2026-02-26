<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();

            // PF ou PJ
            $table->string('type', 2); // PF|PJ

            // PF
            $table->string('full_name', 150)->nullable();
            $table->string('cpf', 11)->nullable(); // só números

            // PJ
            $table->string('company_name', 180)->nullable();
            $table->string('cnpj', 14)->nullable(); // só números
            $table->string('responsible_name', 150)->nullable();
            $table->string('responsible_cpf', 11)->nullable(); // só números

            // Contato
            $table->string('whatsapp', 20)->nullable(); // pode ter +55, espaços, etc (vamos normalizar no form se quiser depois)
            $table->string('email', 180)->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Unicidade dos documentos (somente quando preenchidos).
            // MySQL permite múltiplos NULL em unique, então funciona bem.
            $table->unique('cpf');
            $table->unique('cnpj');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};