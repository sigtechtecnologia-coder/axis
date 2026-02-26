<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="min-w-0">
                <h2 class="truncate font-semibold text-xl text-gray-800 leading-tight">Serviços</h2>
                <p class="mt-1 text-sm text-gray-500">Serviços pré-cadastrados para usar nas propostas.</p>
            </div>

            <a href="{{ route('services.create') }}"
               class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                + Novo serviço
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 rounded-md bg-green-50 p-4 text-green-800">{{ session('success') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <form method="GET" class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Buscar</label>
                            <input type="text" name="q" value="{{ $q }}" placeholder="Nome do serviço..."
                                   class="mt-1 w-80 max-w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div class="flex gap-2">
                            <button class="rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-black">
                                Filtrar
                            </button>
                            <a href="{{ route('services.index') }}"
                               class="rounded-md bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-900 hover:bg-gray-200">
                                Limpar
                            </a>
                        </div>
                    </form>

                    <div class="overflow-x-auto rounded-md border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Nome</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Ativo</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse ($services as $s)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                            {{ $s->name }}
                                            @if($s->description)
                                                <div class="text-xs text-gray-500">
                                                    {{ \Illuminate\Support\Str::limit($s->description, 120) }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-700">
                                            @if ($s->is_active)
                                                <span class="rounded-full bg-green-100 px-2 py-1 text-xs font-semibold text-green-800">Sim</span>
                                            @else
                                                <span class="rounded-full bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-700">Não</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-right text-sm">
                                            <div class="inline-flex items-center gap-3">
                                                <a href="{{ route('services.edit', $s) }}"
                                                   class="font-semibold text-indigo-600 hover:text-indigo-800">
                                                    Editar
                                                </a>
                                                <form method="POST" action="{{ route('services.destroy', $s) }}"
                                                      onsubmit="return confirm('Excluir este serviço?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="font-semibold text-red-600 hover:text-red-800">Excluir</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-6 text-center text-sm text-gray-500">
                                            Nenhum serviço encontrado.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">{{ $services->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>