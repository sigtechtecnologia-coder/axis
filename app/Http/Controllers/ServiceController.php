<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q'));

        $query = Service::query()->orderBy('name');

        if ($q !== '') {
            $query->where('name', 'like', "%{$q}%");
        }

        $services = $query->paginate(20)->withQueryString();

        return view('services.index', [
            'services' => $services,
            'q' => $q,
        ]);
    }

    public function create()
    {
        return view('services.create', [
            'service' => new Service(['is_active' => true]),
        ]);
    }

    public function store(StoreServiceRequest $request)
    {
        Service::create($request->validated());

        return redirect()
            ->route('services.index')
            ->with('success', 'Serviço criado com sucesso.');
    }

    public function edit(Service $service)
    {
        return view('services.edit', [
            'service' => $service,
        ]);
    }

    public function update(UpdateServiceRequest $request, Service $service)
    {
        $service->update($request->validated());

        return redirect()
            ->route('services.index')
            ->with('success', 'Serviço atualizado com sucesso.');
    }

    public function destroy(Service $service)
    {
        $name = $service->name;
        $service->delete();

        return redirect()
            ->route('services.index')
            ->with('success', "Serviço '{$name}' excluído com sucesso.");
    }

    public function show(Service $service)
    {
        abort(404);
    }
}