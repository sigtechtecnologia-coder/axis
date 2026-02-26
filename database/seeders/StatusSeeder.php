<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            // Orçamento
            ['context' => 'quote', 'name' => 'Rascunho',   'sort_order' => 10, 'color' => '#64748b', 'is_active' => true],
            ['context' => 'quote', 'name' => 'Enviado',    'sort_order' => 20, 'color' => '#0284c7', 'is_active' => true],
            ['context' => 'quote', 'name' => 'Aprovado',   'sort_order' => 30, 'color' => '#16a34a', 'is_active' => true],
            ['context' => 'quote', 'name' => 'Reprovado',  'sort_order' => 40, 'color' => '#dc2626', 'is_active' => true],

            // Esteira
            ['context' => 'case',  'name' => 'Recebido',            'sort_order' => 10, 'color' => '#0284c7', 'is_active' => true],
            ['context' => 'case',  'name' => 'Em andamento',        'sort_order' => 20, 'color' => '#f59e0b', 'is_active' => true],
            ['context' => 'case',  'name' => 'Aguardando cliente',  'sort_order' => 30, 'color' => '#7c3aed', 'is_active' => true],
            ['context' => 'case',  'name' => 'Concluído',           'sort_order' => 40, 'color' => '#16a34a', 'is_active' => true],
        ];

        foreach ($items as $data) {
            Status::updateOrCreate(
                ['context' => $data['context'], 'name' => $data['name']],
                $data
            );
        }
    }
}