@php
    $initialItems = old('items');

    if ($initialItems === null) {
        if (isset($quote) && $quote->exists) {
            $initialItems = $quote->quoteServices
                ->map(fn($row) => [
                    'service_id' => $row->service_id,
                    'partner_id' => $row->partner_id,
                    'price' => $row->price,
                ])
                ->values()
                ->toArray();
        } else {
            $initialItems = [
                ['service_id' => '', 'partner_id' => '', 'price' => '0.00'],
            ];
        }
    }

    $servicesList = $services->map(fn($s) => ['id' => $s->id, 'name' => $s->name])->values();
    $partnersList = $partners->map(fn($p) => ['id' => $p->id, 'name' => $p->name])->values();
@endphp

<div class="grid grid-cols-1 gap-4">
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="sm:col-span-1">
            <label class="block text-sm font-medium text-gray-700">Número</label>
            <input type="text"
                   value="{{ old('number', $quote->number) }}"
                   readonly
                   class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <p class="mt-1 text-xs text-gray-500">Gerado automaticamente ao salvar.</p>
        </div>

        <div class="sm:col-span-2">
            <label class="block text-sm font-medium text-gray-700">Cliente</label>
            <select name="client_id"
                    class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">Selecione...</option>
                @foreach ($clients as $c)
                    <option value="{{ $c->id }}" @selected((string)old('client_id', $quote->client_id) === (string)$c->id)>
                        {{ $c->display_name }} ({{ $c->type }})
                    </option>
                @endforeach
            </select>
            @error('client_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Observações</label>
        <textarea name="notes" rows="3"
                  class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes', $quote->notes) }}</textarea>
        @error('notes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="sm:col-span-1">
            <label class="block text-sm font-medium text-gray-700">Desconto (%)</label>
            <input type="number" step="0.01" min="0" max="100" name="discount_percent"
                   value="{{ old('discount_percent', $quote->discount_percent) }}"
                   placeholder="Opcional"
                   class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            @error('discount_percent') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div class="sm:col-span-2 text-sm text-gray-500 flex items-end">
            O total será recalculado ao salvar.
        </div>
    </div>

    {{-- restante do arquivo (itens + script) fica igual ao que você já está usando --}}
    {{-- Copie daqui para baixo do seu arquivo atual sem alterações --}}
    <div class="mt-2">
        <div class="flex items-center justify-between">
            <h3 class="text-base font-semibold text-gray-900">Itens (Serviços)</h3>
            <button type="button" id="add-item"
                    class="rounded-md bg-gray-900 px-3 py-2 text-sm font-semibold text-white hover:bg-black">
                + Adicionar linha
            </button>
        </div>

        @error('items') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror

        <div class="mt-3 overflow-x-auto rounded-md border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Serviço</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Parceiro</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600">Preço (R$)</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600">Ações</th>
                    </tr>
                </thead>
                <tbody id="items-body" class="divide-y divide-gray-200 bg-white"></tbody>
            </table>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
        <div class="rounded-md bg-gray-50 p-4 sm:col-span-1">
            <div class="text-xs text-gray-500">Subtotal</div>
            <div id="subtotalText" class="text-lg font-semibold text-gray-900">R$ 0,00</div>
        </div>
        <div class="rounded-md bg-gray-50 p-4 sm:col-span-1">
            <div class="text-xs text-gray-500">Desconto</div>
            <div id="discountText" class="text-lg font-semibold text-gray-900">R$ 0,00</div>
        </div>
        <div class="rounded-md bg-gray-50 p-4 sm:col-span-1">
            <div class="text-xs text-gray-500">Total</div>
            <div id="totalText" class="text-lg font-semibold text-gray-900">R$ 0,00</div>
        </div>
    </div>
</div>

<script>
(function () {
    if (window.__quoteFormInitialized) return;
    window.__quoteFormInitialized = true;
    const services = @json($servicesList);
    const partners = @json($partnersList);
    const initialItems = @json($initialItems);

    const tbody = document.getElementById('items-body');
    const addBtn = document.getElementById('add-item');

    const discountInput = document.querySelector('input[name="discount_percent"]');

    const subtotalText = document.getElementById('subtotalText');
    const discountText = document.getElementById('discountText');
    const totalText = document.getElementById('totalText');

    function formatBRL(value) {
        const num = isNaN(value) ? 0 : value;
        return num.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    }

    function parseNumber(value) {
        if (value === null || value === undefined) return 0;
        const v = String(value).replace(',', '.');
        const n = parseFloat(v);
        return isNaN(n) ? 0 : n;
    }

    function recalc() {
        let subtotal = 0;

        tbody.querySelectorAll('input[name$="[price]"]').forEach((input) => {
            subtotal += parseNumber(input.value);
        });

        const discountPercent = parseNumber(discountInput?.value || 0);
        const discountAmount = subtotal * (discountPercent / 100);
        const total = subtotal - discountAmount;

        subtotalText.textContent = formatBRL(subtotal);
        discountText.textContent = formatBRL(discountAmount);
        totalText.textContent = formatBRL(total);
    }

    function buildSelect(options, name, selectedValue, placeholder) {
        const select = document.createElement('select');
        select.name = name;
        select.className = 'w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500';

        const opt0 = document.createElement('option');
        opt0.value = '';
        opt0.textContent = placeholder;
        select.appendChild(opt0);

        options.forEach((o) => {
            const opt = document.createElement('option');
            opt.value = o.id;
            opt.textContent = o.name;
            if (String(selectedValue) === String(o.id)) opt.selected = true;
            select.appendChild(opt);
        });

        return select;
    }

    function addRow(item = {service_id: '', partner_id: '', price: '0.00'}) {
        const index = tbody.children.length;
        const tr = document.createElement('tr');

        const tdService = document.createElement('td');
        tdService.className = 'px-4 py-3';
        tdService.appendChild(buildSelect(services, `items[${index}][service_id]`, item.service_id, 'Selecione...'));
        tr.appendChild(tdService);

        const tdPartner = document.createElement('td');
        tdPartner.className = 'px-4 py-3';
        tdPartner.appendChild(buildSelect(partners, `items[${index}][partner_id]`, item.partner_id, 'Selecione...'));
        tr.appendChild(tdPartner);

        const tdPrice = document.createElement('td');
        tdPrice.className = 'px-4 py-3 text-right';
        const inputPrice = document.createElement('input');
        inputPrice.type = 'number';
        inputPrice.step = '0.01';
        inputPrice.min = '0';
        inputPrice.name = `items[${index}][price]`;
        inputPrice.value = item.price ?? '0.00';
        inputPrice.className = 'w-40 max-w-full text-right rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500';
        inputPrice.addEventListener('input', recalc);
        tdPrice.appendChild(inputPrice);
        tr.appendChild(tdPrice);

        const tdActions = document.createElement('td');
        tdActions.className = 'px-4 py-3 text-right';
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.textContent = 'Remover';
        btn.className = 'font-semibold text-red-600 hover:text-red-800';
        btn.addEventListener('click', () => {
            tr.remove();
            normalizeIndexes();
            recalc();
        });
        tdActions.appendChild(btn);
        tr.appendChild(tdActions);

        tbody.appendChild(tr);
        recalc();
    }

    function normalizeIndexes() {
        [...tbody.children].forEach((tr, newIndex) => {
            const serviceSelect = tr.querySelector('select[name*="[service_id]"]');
            const partnerSelect = tr.querySelector('select[name*="[partner_id]"]');
            const priceInput = tr.querySelector('input[name*="[price]"]');

            if (serviceSelect) serviceSelect.name = `items[${newIndex}][service_id]`;
            if (partnerSelect) partnerSelect.name = `items[${newIndex}][partner_id]`;
            if (priceInput) priceInput.name = `items[${newIndex}][price]`;
        });
    }

    addBtn?.addEventListener('click', () => addRow());
    discountInput?.addEventListener('input', recalc);

    (initialItems || []).forEach(addRow);
    if (!initialItems || initialItems.length === 0) addRow();

    recalc();
})();
</script>