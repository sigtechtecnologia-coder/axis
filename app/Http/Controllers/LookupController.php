<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Partner;
use App\Models\Service;
use Illuminate\Http\Request;

class LookupController extends Controller
{
    public function clients(Request $request)
    {
        $type = $request->query('type'); // PF|PJ
        $q = trim((string)$request->query('q'));

        $query = Client::query()->select(['id', 'type', 'full_name', 'company_name', 'cpf', 'cnpj']);

        if ($type && in_array($type, Client::types(), true)) {
            $query->where('type', $type);
        }

        if ($q !== '') {
            $digits = preg_replace('/\D+/', '', $q);

            $query->where(function ($sub) use ($q, $digits) {
                $sub->where('full_name', 'like', "%{$q}%")
                    ->orWhere('company_name', 'like', "%{$q}%");

                if ($digits !== '') {
                    $sub->orWhere('cpf', $digits)->orWhere('cnpj', $digits);
                }
            });
        }

        $items = $query->orderByDesc('id')->limit(20)->get()->map(function ($c) {
            $name = $c->type === 'PJ' ? ($c->company_name ?? '') : ($c->full_name ?? '');
            $doc = $c->type === 'PJ' ? ($c->cnpj ?? '') : ($c->cpf ?? '');

            return [
                'id' => $c->id,
                'type' => $c->type,
                'label' => trim("#{$c->id} - {$name}" . ($doc ? " ({$doc})" : '')),
            ];
        });

        return response()->json(['data' => $items]);
    }

    public function services(Request $request)
    {
        $q = trim((string)$request->query('q'));

        $query = Service::query()->select(['id', 'name'])->where('is_active', true);

        if ($q !== '') {
            $query->where('name', 'like', "%{$q}%");
        }

        $items = $query->orderBy('name')->limit(20)->get()->map(fn ($s) => [
            'id' => $s->id,
            'label' => $s->name,
        ]);

        return response()->json(['data' => $items]);
    }

    public function partners(Request $request)
    {
        $q = trim((string)$request->query('q'));

        $query = Partner::query()->select(['id', 'type', 'name', 'cpf', 'cnpj'])->where('is_active', true);

        if ($q !== '') {
            $digits = preg_replace('/\D+/', '', $q);

            $query->where(function ($sub) use ($q, $digits) {
                $sub->where('name', 'like', "%{$q}%");

                if ($digits !== '') {
                    $sub->orWhere('cpf', $digits)->orWhere('cnpj', $digits);
                }
            });
        }

        $items = $query->orderBy('name')->limit(20)->get()->map(function ($p) {
            $doc = $p->type === 'PJ' ? ($p->cnpj ?? '') : ($p->cpf ?? '');

            return [
                'id' => $p->id,
                'type' => $p->type,
                'label' => trim("#{$p->id} - {$p->name}" . ($doc ? " ({$doc})" : '')),
            ];
        });

        return response()->json(['data' => $items]);
    }
}