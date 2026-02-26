<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Proposta {{ $quote->number }}</h2>
                <p class="text-sm text-gray-500">
                    Cliente: <span class="font-semibold">{{ $quote->client?->display_name }}</span>
                    • Status: <span class="font-semibold">{{ $quote->status?->name }}</span>
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('quotes.edit', $quote) }}"
                   class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                    Editar
                </a>
                <a href="{{ route('quotes.index') }}"
                   class="rounded-md bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-900 hover:bg-gray-200">
                    Voltar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 rounded-md bg-green-50 p-4 text-green-800">{{ session('success') }}</div>
            @endif

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg lg:col-span-2">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-base font-semibold text-gray-900">Itens</h3>

                        <div class="mt-3 overflow-x-auto rounded-md border border-gray-200">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Serviço</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Parceiro</th>
                                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600">Preço</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @forelse($quote->quoteServices as $row)
                                        <tr>
                                            <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                                {{ $row->service?->name ?? '-' }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-700">
                                                {{ $row->partner?->name ?? '-' }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-700 text-right">
                                                R$ {{ number_format((float)$row->price, 2, ',', '.') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-4 py-6 text-center text-sm text-gray-500">
                                                Nenhum item.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($quote->notes)
                            <div class="mt-6">
                                <h4 class="text-sm font-semibold text-gray-900">Observações</h4>
                                <div class="mt-1 text-sm text-gray-700 whitespace-pre-line">{{ $quote->notes }}</div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-base font-semibold text-gray-900">Totais</h3>

                        <dl class="mt-4 space-y-3">
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600">Subtotal</dt>
                                <dd class="text-sm font-semibold text-gray-900">R$ {{ number_format((float)$quote->subtotal, 2, ',', '.') }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600">Desconto (%)</dt>
                                <dd class="text-sm font-semibold text-gray-900">
                                    {{ $quote->discount_percent === null ? '-' : number_format((float)$quote->discount_percent, 2, ',', '.') . '%' }}
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600">Desconto (R$)</dt>
                                <dd class="text-sm font-semibold text-gray-900">R$ {{ number_format((float)$quote->discount_amount, 2, ',', '.') }}</dd>
                            </div>
                            <div class="flex justify-between border-t pt-3">
                                <dt class="text-sm text-gray-900 font-semibold">Total</dt>
                                <dd class="text-sm text-gray-900 font-bold">R$ {{ number_format((float)$quote->total, 2, ',', '.') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>