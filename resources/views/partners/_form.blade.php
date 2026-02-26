@php
    $types = $types ?? ['PF' => 'Pessoa Física', 'PJ' => 'Pessoa Jurídica'];
@endphp

<div class="grid grid-cols-1 gap-4">
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label class="block text-sm font-medium text-gray-700">Tipo</label>
            <select id="partner_type" name="type"
                    class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @foreach ($types as $key => $label)
                    <option value="{{ $key }}" @selected(old('type', $partner->type) === $key)>{{ $label }}</option>
                @endforeach
            </select>
            @error('type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-end">
            <label class="inline-flex items-center gap-2">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1"
                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                       @checked(old('is_active', $partner->is_active) ? true : false)>
                <span class="text-sm font-medium text-gray-700">Ativo</span>
            </label>
            @error('is_active') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Nome / Razão social</label>
        <input type="text" name="name" value="{{ old('name', $partner->name) }}"
               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div id="panel_pf" class="rounded-md border border-gray-200 p-4">
        <h3 class="mb-3 text-sm font-semibold text-gray-900">Documento (PF)</h3>
        <label class="block text-sm font-medium text-gray-700">CPF</label>
        <input id="partner_cpf" type="text" name="cpf" value="{{ old('cpf', $partner->cpf) }}"
               placeholder="000.000.000-00"
               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        @error('cpf') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div id="panel_pj" class="rounded-md border border-gray-200 p-4">
        <h3 class="mb-3 text-sm font-semibold text-gray-900">Documento (PJ)</h3>
        <label class="block text-sm font-medium text-gray-700">CNPJ</label>
        <input id="partner_cnpj" type="text" name="cnpj" value="{{ old('cnpj', $partner->cnpj) }}"
               placeholder="00.000.000/0000-00"
               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        @error('cnpj') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="rounded-md border border-gray-200 p-4">
        <h3 class="mb-3 text-sm font-semibold text-gray-900">Contato</h3>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-gray-700">WhatsApp</label>
                <input type="text" name="whatsapp" value="{{ old('whatsapp', $partner->whatsapp) }}"
                       class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('whatsapp') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" value="{{ old('email', $partner->email) }}"
                       class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Observações</label>
                <textarea name="notes" rows="3"
                          class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes', $partner->notes) }}</textarea>
                @error('notes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const typeEl = document.getElementById('partner_type');
    const pfPanel = document.getElementById('panel_pf');
    const pjPanel = document.getElementById('panel_pj');

    function onlyDigits(v) { return (v || '').replace(/\D+/g, ''); }

    function maskCPF(el) {
        if (!el) return;
        el.addEventListener('input', function () {
            let v = onlyDigits(el.value).slice(0, 11);
            v = v.replace(/(\d{3})(\d)/, '$1.$2');
            v = v.replace(/(\d{3})\.(\d{3})(\d)/, '$1.$2.$3');
            v = v.replace(/(\d{3})\.(\d{3})\.(\d{3})(\d{1,2})$/, '$1.$2.$3-$4');
            el.value = v;
        });
        el.dispatchEvent(new Event('input'));
    }

    function maskCNPJ(el) {
        if (!el) return;
        el.addEventListener('input', function () {
            let v = onlyDigits(el.value).slice(0, 14);
            v = v.replace(/^(\d{2})(\d)/, '$1.$2');
            v = v.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
            v = v.replace(/\.(\d{3})(\d)/, '.$1/$2');
            v = v.replace(/(\d{4})(\d{1,2})$/, '$1-$2');
            el.value = v;
        });
        el.dispatchEvent(new Event('input'));
    }

    function setPanels() {
        const type = typeEl.value;
        if (type === 'PF') {
            pfPanel.style.display = '';
            pjPanel.style.display = 'none';
        } else {
            pfPanel.style.display = 'none';
            pjPanel.style.display = '';
        }
    }

    typeEl.addEventListener('change', setPanels);
    setPanels();

    maskCPF(document.getElementById('partner_cpf'));
    maskCNPJ(document.getElementById('partner_cnpj'));
})();
</script>