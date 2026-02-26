<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar proposta</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('quotes.update', $quote) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        @include('quotes._form', [
                            'quote' => $quote,
                            'clients' => $clients,
                            'services' => $services,
                            'partners' => $partners,
                        ])

                        <div class="flex items-center gap-3">
                            <button class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                                Salvar
                            </button>
                            <a href="{{ route('quotes.show', $quote) }}"
                               class="rounded-md bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-900 hover:bg-gray-200">
                                Voltar
                            </a>
                        </div>
                    </form>

                    @if ($errors->any())
                        <div class="mt-6 rounded-md bg-red-50 p-4 text-red-800">
                            Verifique os erros no formulário.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>