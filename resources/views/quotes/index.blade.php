<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Propostas</h2>
                <p class="text-sm text-gray-500">Gerencie propostas e seus serviços.</p>
            </div>
            <a href="{{ route('quotes.create') }}"
               class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                + Nova proposta
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
                    <div class="overflow-x-auto rounded-md border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Número</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Cliente</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Status</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600">Total</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse ($quotes as $q)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                            <a class="text-indigo-600 hover:text-indigo-800 font-semibold" href="{{ route('quotes.show', $q) }}">
                                                {{ $q->number }}
                                            </a>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ $q->client?->display_name ?? '-' }}</td>

                                        <td class="px-4 py-3 text-sm text-gray-700">
                                            <form method="POST" action="{{ route('quotes.update-status', $q) }}">
                                                @csrf
                                                @method('PATCH')
                                                <select name="status_id"
                                                        onchange="this.form.submit()"
                                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                    @foreach($statuses as $st)
                                                        <option value="{{ $st->id }}" @selected((string)$q->status_id === (string)$st->id)>
                                                            {{ $st->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </form>
                                        </td>

                                        <td class="px-4 py-3 text-sm text-gray-700 text-right">R$ {{ number_format((float)$q->total, 2, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-right text-sm">
                                            <div class="inline-flex items-center gap-3">
                                                <a href="{{ route('quotes.edit', $q) }}" class="font-semibold text-indigo-600 hover:text-indigo-800">Editar</a>
                                                <form method="POST" action="{{ route('quotes.destroy', $q) }}" onsubmit="return confirm('Excluir esta proposta?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="font-semibold text-red-600 hover:text-red-800">Excluir</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">Nenhuma proposta encontrada.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">{{ $quotes->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>