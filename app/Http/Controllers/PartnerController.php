<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePartnerRequest;
use App\Http\Requests\UpdatePartnerRequest;
use App\Models\Partner;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type'); // PF|PJ|null
        $q = trim((string)$request->query('q'));

        $query = Partner::query()->orderBy('name');

        if ($type && in_array($type, Partner::types(), true)) {
            $query->where('type', $type);
        }

        if ($q !== '') {
            $digits = preg_replace('/\D+/', '', $q);

            $query->where(function ($sub) use ($q, $digits) {
                $sub->where('name', 'like', "%{$q}%");

                if ($digits !== '') {
                    $sub->orWhere('cpf', $digits)->orWhere('cnpj', $digits);
                }
            });
        }

        $partners = $query->paginate(20)->withQueryString();

        return view('partners.index', [
            'partners' => $partners,
            'type' => $type,
            'q' => $q,
            'types' => [
                Partner::TYPE_PF => 'Pessoa Física',
                Partner::TYPE_PJ => 'Pessoa Jurídica',
            ],
        ]);
    }

    public function create(Request $request)
    {
        $type = $request->query('type');

        return view('partners.create', [
            'partner' => new Partner([
                'type' => in_array($type, Partner::types(), true) ? $type : Partner::TYPE_PJ,
                'is_active' => true,
            ]),
            'types' => [
                Partner::TYPE_PF => 'Pessoa Física',
                Partner::TYPE_PJ => 'Pessoa Jurídica',
            ],
        ]);
    }

    public function store(StorePartnerRequest $request)
    {
        $partner = Partner::create($request->validated());

        return redirect()
            ->route('partners.index', ['type' => $partner->type])
            ->with('success', 'Parceiro criado com sucesso.');
    }

    public function edit(Partner $partner)
    {
        return view('partners.edit', [
            'partner' => $partner,
            'types' => [
                Partner::TYPE_PF => 'Pessoa Física',
                Partner::TYPE_PJ => 'Pessoa Jurídica',
            ],
        ]);
    }

    public function update(UpdatePartnerRequest $request, Partner $partner)
    {
        $partner->update($request->validated());

        return redirect()
            ->route('partners.index', ['type' => $partner->type])
            ->with('success', 'Parceiro atualizado com sucesso.');
    }

    public function destroy(Partner $partner)
    {
        $name = $partner->name;
        $partner->delete();

        return redirect()
            ->route('partners.index')
            ->with('success', "Parceiro '{$name}' excluído com sucesso.");
    }

    public function show(Partner $partner)
    {
        abort(404);
    }
}