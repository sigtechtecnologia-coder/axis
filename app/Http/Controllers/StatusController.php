<?php

namespace App\Http\Controllers;

use App\Models\Status;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    public function index(Request $request)
    {
        $context = $request->query('context'); // quote|case|null

        $query = Status::query()->orderBy('context')->orderBy('sort_order')->orderBy('name');

        if ($context && in_array($context, Status::contexts(), true)) {
            $query->where('context', $context);
        }

        $statuses = $query->paginate(20)->withQueryString();

        return view('statuses.index', [
            'statuses' => $statuses,
            'context' => $context,
            'contexts' => [
                Status::CONTEXT_QUOTE => 'Orçamento',
                Status::CONTEXT_CASE => 'Esteira',
            ],
        ]);
    }

    public function create(Request $request)
    {
        $context = $request->query('context');

        return view('statuses.create', [
            'status' => new Status([
                'context' => in_array($context, Status::contexts(), true) ? $context : Status::CONTEXT_QUOTE,
                'is_active' => true,
                'sort_order' => 10,
                'color' => '#0284c7',
            ]),
            'contexts' => [
                Status::CONTEXT_QUOTE => 'Orçamento',
                Status::CONTEXT_CASE => 'Esteira',
            ],
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        Status::create($data);

        return redirect()
            ->route('statuses.index', ['context' => $data['context']])
            ->with('success', 'Status criado com sucesso.');
    }

    public function edit(Status $status)
    {
        return view('statuses.edit', [
            'status' => $status,
            'contexts' => [
                Status::CONTEXT_QUOTE => 'Orçamento',
                Status::CONTEXT_CASE => 'Esteira',
            ],
        ]);
    }

    public function update(Request $request, Status $status)
    {
        $data = $this->validated($request);

        $status->update($data);

        return redirect()
            ->route('statuses.index', ['context' => $status->context])
            ->with('success', 'Status atualizado com sucesso.');
    }

    public function destroy(Status $status)
    {
        $context = $status->context;
        $name = $status->name;

        $status->delete();

        return redirect()
            ->route('statuses.index', ['context' => $context])
            ->with('success', "Status '{$name}' excluído com sucesso.");
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'context' => ['required', 'in:' . implode(',', Status::contexts())],
            'name' => ['required', 'string', 'max:120'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999999'],
            'color' => ['nullable', 'string', 'max:20'],
            'is_active' => ['required', 'boolean'],
        ], [
            'context.in' => 'Contexto inválido.',
        ]);
    }
}