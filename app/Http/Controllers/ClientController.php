<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type'); // PF|PJ|null
        $q = trim((string)$request->query('q'));

        $query = Client::query()->orderByDesc('id');

        if ($type && in_array($type, Client::types(), true)) {
            $query->where('type', $type);
        }

        if ($q !== '') {
            $digits = preg_replace('/\D+/', '', $q);

            $query->where(function ($sub) use ($q, $digits) {
                $sub->where('full_name', 'like', "%{$q}%")
                    ->orWhere('company_name', 'like', "%{$q}%")
                    ->orWhere('responsible_name', 'like', "%{$q}%");

                if ($digits !== '') {
                    $sub->orWhere('cpf', $digits)
                        ->orWhere('cnpj', $digits)
                        ->orWhere('responsible_cpf', $digits);
                }
            });
        }

        $clients = $query->paginate(15)->withQueryString();

        return view('clients.index', [
            'clients' => $clients,
            'type' => $type,
            'q' => $q,
            'types' => [
                Client::TYPE_PF => 'Pessoa Física',
                Client::TYPE_PJ => 'Pessoa Jurídica',
            ],
        ]);
    }

    public function create(Request $request)
    {
        $type = $request->query('type');

        return view('clients.create', [
            'client' => new Client([
                'type' => in_array($type, Client::types(), true) ? $type : Client::TYPE_PF,
                'is_active' => true,
            ]),
            'types' => [
                Client::TYPE_PF => 'Pessoa Física',
                Client::TYPE_PJ => 'Pessoa Jurídica',
            ],
        ]);
    }

    public function store(StoreClientRequest $request)
    {
        $client = Client::create($request->validated());

        return redirect()
            ->route('clients.index', ['type' => $client->type])
            ->with('success', 'Cliente criado com sucesso.');
    }

    public function edit(Client $client)
    {
        return view('clients.edit', [
            'client' => $client,
            'types' => [
                Client::TYPE_PF => 'Pessoa Física',
                Client::TYPE_PJ => 'Pessoa Jurídica',
            ],
        ]);
    }

    public function update(UpdateClientRequest $request, Client $client)
    {
        $client->update($request->validated());

        return redirect()
            ->route('clients.index', ['type' => $client->type])
            ->with('success', 'Cliente atualizado com sucesso.');
    }

    public function destroy(Client $client)
    {
        $name = $client->displayName();
        $client->delete();

        return redirect()
            ->route('clients.index')
            ->with('success', "Cliente '{$name}' excluído com sucesso.");
    }

    public function show(Client $client)
    {
        abort(404);
    }
}