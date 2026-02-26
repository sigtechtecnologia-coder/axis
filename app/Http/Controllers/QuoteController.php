<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Partner;
use App\Models\Quote;
use App\Models\Service;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class QuoteController extends Controller
{
    public function index()
    {
        $quotes = Quote::query()
            ->with(['client', 'status', 'quoteServices.service', 'quoteServices.partner'])
            ->orderByDesc('id')
            ->paginate(20);

        // Statuses para o "quick edit" na listagem
        $statuses = Status::orderBy('sort_order')->get();

        return view('quotes.index', compact('quotes', 'statuses'));
    }

    public function create()
    {
        $quote = new Quote([
            'number' => $this->previewNextNumber(),
            'discount_percent' => null,
        ]);

        return view('quotes.create', [
            'quote' => $quote,
            'clients' => Client::query()
                ->orderBy('type')
                ->orderBy('full_name')
                ->orderBy('company_name')
                ->get(),
            'services' => Service::orderBy('name')->get(),
            'partners' => Partner::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedDataForStore($request);

        return DB::transaction(function () use ($data) {
            $statusId = $this->getDefaultStatusId();

            // gera o número dentro da transaction para reduzir chance de colisão
            $number = $this->generateNextNumber();

            /** @var Quote $quote */
            $quote = Quote::create([
                'number' => $number,
                'client_id' => $data['client_id'],
                'status_id' => $statusId,
                'notes' => $data['notes'] ?? null,
                'discount_percent' => $data['discount_percent'] ?? null,
                'subtotal' => 0,
                'discount_amount' => 0,
                'total' => 0,
            ]);

            foreach ($data['items'] as $item) {
                $quote->quoteServices()->create([
                    'service_id' => $item['service_id'],
                    'partner_id' => $item['partner_id'],
                    'price' => $item['price'],
                ]);
            }

            $quote->load('quoteServices');
            $quote->recalcTotals();
            $quote->save();

            return redirect()
                ->route('quotes.show', $quote)
                ->with('success', 'Proposta criada com sucesso.');
        });
    }

    public function show(Quote $quote)
    {
        $quote->load([
            'client',
            'status',
            'quoteServices.service',
            'quoteServices.partner',
        ]);

        return view('quotes.show', compact('quote'));
    }

    public function edit(Quote $quote)
    {
        $quote->load('quoteServices');

        return view('quotes.edit', [
            'quote' => $quote,
            'clients' => Client::query()
                ->orderBy('type')
                ->orderBy('full_name')
                ->orderBy('company_name')
                ->get(),
            'services' => Service::orderBy('name')->get(),
            'partners' => Partner::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Quote $quote)
    {
        $data = $this->validatedDataForUpdate($request, $quote);

        return DB::transaction(function () use ($data, $quote) {
            $quote->update([
                // number não edita
                'client_id' => $data['client_id'],
                // status não edita aqui (vai ser quick edit na listagem)
                'notes' => $data['notes'] ?? null,
                'discount_percent' => $data['discount_percent'] ?? null,
            ]);

            $quote->quoteServices()->delete();

            foreach ($data['items'] as $item) {
                $quote->quoteServices()->create([
                    'service_id' => $item['service_id'],
                    'partner_id' => $item['partner_id'],
                    'price' => $item['price'],
                ]);
            }

            $quote->load('quoteServices');
            $quote->recalcTotals();
            $quote->save();

            return redirect()
                ->route('quotes.show', $quote)
                ->with('success', 'Proposta atualizada com sucesso.');
        });
    }

    public function destroy(Quote $quote)
    {
        $quote->delete();

        return redirect()
            ->route('quotes.index')
            ->with('success', 'Proposta excluída com sucesso.');
    }

    /**
     * Atualização rápida do status na listagem.
     */
    public function updateStatus(Request $request, Quote $quote)
    {
        $data = $request->validate([
            'status_id' => ['required', 'integer', Rule::exists('statuses', 'id')],
        ]);

        $quote->update([
            'status_id' => $data['status_id'],
        ]);

        return redirect()
            ->route('quotes.index')
            ->with('success', 'Status atualizado com sucesso.');
    }

    private function validatedDataForStore(Request $request): array
    {
        return $request->validate([
            'client_id' => ['required', 'integer', Rule::exists('clients', 'id')],
            'notes' => ['nullable', 'string'],
            'discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.service_id' => ['required', 'integer', Rule::exists('services', 'id')],
            'items.*.partner_id' => ['required', 'integer', Rule::exists('partners', 'id')],
            'items.*.price' => ['required', 'numeric', 'min:0'],
        ], [
            'items.required' => 'Adicione pelo menos um item (serviço).',
            'items.min' => 'Adicione pelo menos um item (serviço).',
        ]);
    }

    private function validatedDataForUpdate(Request $request, Quote $quote): array
    {
        return $request->validate([
            'client_id' => ['required', 'integer', Rule::exists('clients', 'id')],
            'notes' => ['nullable', 'string'],
            'discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.service_id' => ['required', 'integer', Rule::exists('services', 'id')],
            'items.*.partner_id' => ['required', 'integer', Rule::exists('partners', 'id')],
            'items.*.price' => ['required', 'numeric', 'min:0'],
        ]);
    }

    private function getDefaultStatusId(): int
    {
        $status = Status::query()
            ->where('name', 'Aguardando cliente')
            ->first();

        if (!$status) {
            abort(500, "Status padrão 'Aguardando cliente' não encontrado na tabela statuses.");
        }

        return (int) $status->id;
    }

    private function previewNextNumber(): string
    {
        // Apenas para mostrar no form (não é “garantia” contra concorrência)
        return $this->formatNumber(now()->year, $this->getNextSequenceForYear(now()->year));
    }

    private function generateNextNumber(): string
    {
        $year = now()->year;

        // retry simples se houver colisão por concorrência
        for ($i = 0; $i < 5; $i++) {
            $seq = $this->getNextSequenceForYear($year);
            $number = $this->formatNumber($year, $seq);

            $exists = Quote::query()->where('number', $number)->exists();
            if (!$exists) {
                return $number;
            }
        }

        abort(500, 'Não foi possível gerar um número único de proposta. Tente novamente.');
    }

    private function getNextSequenceForYear(int $year): int
    {
        $prefix = "AX-{$year}-";

        $last = Quote::query()
            ->where('number', 'like', $prefix . '%')
            ->orderByDesc('number')
            ->value('number');

        if (!$last) {
            return 1;
        }

        $parts = explode('-', $last);
        $seqPart = end($parts);
        $seq = (int) ltrim((string) $seqPart, '0');

        return $seq + 1;
    }

    private function formatNumber(int $year, int $seq): string
    {
        return sprintf('AX-%d-%04d', $year, $seq);
    }
}